<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >
  <link
    rel="icon"
    {{--
    href="{{asset('/favicon.ico')}}"
    --}}
    type="image/x-icon"
  >
  <title> Gym Manager</title>
  <script src="https://js.stripe.com/v3/"></script>

</head>

<body>
  <div id="app"></div>

  @vite(['resources/js/app.js'])
</body>

</html>