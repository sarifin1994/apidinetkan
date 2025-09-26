<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Radiusqu" />
<meta name="author" content="Putra Garsel Interkoneksi" />

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>

<link rel="icon" type="image/x-icon" href="{{ asset('assets/old/radiusqu/img/favicon.png') }}" />

<!-- Google font-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100;200;300;400;500;600;700;800;900&amp;display=swap"
  rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap"
  rel="stylesheet">

<script>
  class UrlBuilder {
    constructor(baseUrl) {
      this.segments = baseUrl.replace(/\/$/, '').split('/');
    }

    // Automatically converts to string when needed
    toString() {
      return this.segments.join('/');
    }

    [Symbol.toPrimitive](hint) {
      if (hint === 'string') {
        return this.toString();
      }
      return this.segments.join('/');
    }

    // Add a new segment
    push(segment) {
      if (segment) {
        this.segments.push(segment.trim('/'));
      }
      return this;
    }

    // Remove the last segment
    pop() {
      this.segments.pop();
      return this;
    }

    // Replace the current segment with another
    replace(segment, newSegment) {
      const index = this.segments.indexOf(segment);

      if (index >= 0) {
        this.segments[index] = newSegment.trim('/');
      }

      return this;
    }

    // Get the current segment array (optional utility)
    segments() {
      return [...this.segments];
    }

    // Get the path domain origin and first segment
    base() {
      return this.segments.slice(0, 4).join('/');
    }

    clone() {
      return new UrlBuilder(this.toString());
    }
  }

  const baseUrl = new UrlBuilder(window.location.origin + window.location.pathname.trim('/'));
  const formatter = new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 0
  });
</script>

<!-- latest jquery-->
<script src="{{ asset('assets/old/js/jquery.min.js') }}"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<style>
     #map {
    width: 100%;
    height: 500px; /* Pastikan tinggi cukup besar */
  }
    </style>
