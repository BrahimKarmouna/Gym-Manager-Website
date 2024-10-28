import { extend } from "quasar";
import { api } from "../boot/axios";
import { reactive, toValue, watchEffect } from "vue";

/**
 * @param {Object|Function} fields
 *
 * @returns
 */
export function useForm(fields = {}) {
  let recentlySuccessfulTimeoutId;

  let defaults =
    typeof fields === "object"
      ? extend(true, {}, fields)
      : extend(true, {}, fields());

  const form = reactive({
    fields: extend(true, {}, defaults),
    errors: {},
    processing: false,
    recentlySuccessful: false,
    hasErrors: false,

    async submit(submitter, options = {}) {
      if (this.processing) return;

      this.recentlySuccessful = false;
      clearTimeout(recentlySuccessfulTimeoutId);

      if (options.onBefore) {
        await options.onBefore();
      }

      this.processing = true;

      if (options.onStart) {
        await options.onStart();
      }

      try {
        const response = await submitter(this.fields);

        this.recentlySuccessful = true;

        recentlySuccessfulTimeoutId = setTimeout(() => {
          this.recentlySuccessful = false;
        }, 2000);

        this.clearErrors();

        if (options.onSuccess) {
          await options.onSuccess(response);
        }

        defaults = extend(
          true,
          {},
          typeof fields === "object" ? fields : fields()
        );

        return response;
      } catch (err) {
        this.processing = false;
        this.progress = null;

        let errors = {};

        if (err.response.status === 422) {
          errors = err.response.data.errors;
        }

        this.clearErrors().setError(errors);

        if (options.onError) {
          options.onError(errors);
        }

        return Promise.reject(errors);
      } finally {
        this.processing = false;
        this.progress = null;

        if (options.onFinish) {
          return options.onFinish();
        }
      }
    },

    async post(uri, config = {}, options = {}) {
      return this.submit((fields) => api.post(uri, fields, config), options);
    },

    async put(uri, config = {}, options = {}) {
      return this.submit((fields) => api.put(uri, fields, config), options);
    },

    async patch(uri, config = {}, options = {}) {
      return this.submit((fields) => api.patch(uri, fields, config), options);
    },

    async delete(uri, config = {}, options = {}) {
      return this.submit(
        (fields) =>
          api.delete(uri, {
            data: fields,
            ...config,
          }),

        options
      );
    },

    defaults(fieldOrFields = undefined, maybeValue = undefined) {
      if (typeof fields === "function") {
        throw new Error(
          "You cannot call `defaults()` when using a function to define your form fields."
        );
      }

      if (typeof fieldOrFields === "undefined") {
        defaults = this.data();
      } else {
        defaults = Object.assign(
          {},
          extend(true, {}, defaults),
          typeof fieldOrFields === "string"
            ? { [fieldOrFields]: maybeValue }
            : fieldOrFields
        );
      }

      return this;
    },

    data() {
      return Object.keys(defaults).reduce((carry, key) => {
        carry[key] = this.fields[key];
        return carry;
      }, {});
    },

    reset(...fields) {
      const resolvedData =
        typeof fields === "object"
          ? extend(true, {}, defaults)
          : extend(true, {}, fields());
      const clonedData = extend(true, {}, resolvedData);
      if (fields.length === 0) {
        defaults = clonedData;
        Object.assign(this.fields, resolvedData);
      } else {
        Object.keys(resolvedData)
          .filter((key) => fields.includes(key))
          .forEach((key) => {
            defaults[key] = clonedData[key];
            this.fields[key] = resolvedData[key];
          });
      }

      return this;
    },

    setError(fieldOrFields = undefined, maybeValue = undefined) {
      Object.assign(
        this.errors,
        typeof fieldOrFields === "string"
          ? { [fieldOrFields]: maybeValue }
          : fieldOrFields
      );

      this.hasErrors = Object.keys(this.errors).length > 0;

      return this;
    },

    clearErrors(...fields) {
      this.errors = Object.keys(this.errors).reduce(
        (carry, field) => ({
          ...carry,
          ...(fields.length > 0 && !fields.includes(field)
            ? { [field]: this.errors[field] }
            : {}),
        }),
        {}
      );

      this.hasErrors = Object.keys(this.errors).length > 0;

      return this;
    },

    clearSuccess() {
      clearTimeout(recentlySuccessfulTimeoutId);
      this.recentlySuccessful = false;
    },
  });

  watchEffect(() => {
    form.fields = extend(true, {}, toValue(fields));
    defaults = extend(true, {}, toValue(fields));
  });

  return form;
}
