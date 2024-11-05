import { ref, toValue, watchEffect } from "vue";

export function useMoney(currency = "USD", locale = "en-US") {
  const _locale = ref(toValue(locale));
  const _currency = ref(toValue(currency));

  let formatter = new Intl.NumberFormat(_locale.value, {
    style: "currency",
    currency: currency,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  });

  function format(value) {
    return formatter.format(value);
  }

  watchEffect(() => {
    _locale.value = toValue(locale);
    _currency.value = toValue(currency);

    formatter = new Intl.NumberFormat(_locale.value, {
      style: "currency",
      currency: _currency.value,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  });

  return {
    locale: _locale,
    currency: _currency,
    format,
  };
}
