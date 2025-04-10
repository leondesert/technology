(function($) {
  $(document).ready(function() {
      var table = new DataTable('#dashboard', {
          language: {
              url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json',
          },
          dom: 'Bt',
          scrollY: true,
          scrollCollapse: true,
          scrollX: true,
          ordering: false,
      });
  });
})(jQuery);
