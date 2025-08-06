(function($) {
  $(document).ready(function() {
      var table = new DataTable('#dashboard', {
          language: {
              url: '/js/datatables/i18n/ru.json',
          },
          dom: 'Bt',
          scrollY: true,
          scrollCollapse: true,
          scrollX: true,
          ordering: false,
      });
  });
})(jQuery);
