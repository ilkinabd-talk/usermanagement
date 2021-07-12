<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <link href="/images/logo.svg" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="Tinker admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
    <meta name="keywords"
          content="admin template, Tinker Admin Template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="LEFT4CODE">
    <title>{{ tag.getTitle() }}</title>

    {{ assets.outputCss('css') }}
</head>
<body class="{% if bodyClass is defined %}{{ bodyClass }}{% endif %}">
{{ content() }}

{{ assets.outputJs('js') }}
</body>
</html>
