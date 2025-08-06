

(function($){

$(document).ready(function() {


  var fields = [
              // { 
              //   label: 'passengers_id:',
              //   name: 'passengers_id',
              // },
              { 
                label: 'ФИО:',
                name: 'fio',
              },
              { 
                label: 'Фамилия:',
                name: 'surname',
              },
              { 
                label: 'Имя:',
                name: 'name',
              },
              { 
                label: 'Пасспорт:',
                name: 'pass',
              },
              { 
                label: 'Тип пассажира:',
                name: 'pas_type',
              },
              { 
                label: 'Льгота:',
                name: 'benefit_doc',
              },
              { 
                label: 'Дата рождения:',
                name: 'birth_date',
              },
              { 
                label: 'Пол:',
                name: 'gender',
              },
              { 
                label: 'Гражданство:',
                name: 'citizenship',
              },
              { 
                label: 'Контакты:',
                name: 'contact',
              },
  ];

  

  if (role === 'superadmin') {
    fields.unshift(
      { 
        label: 'passengers_id:',
        name: 'passengers_id',
      },
    );
  }



	var editor = new DataTable.Editor( {
		ajax: {
            url: '..//php/passengers.php',
            type: 'POST',
            data: function(d) {
                d.key = myVar;
                d.colum_name = colum_name;
            }
        },
    table: '#passengers',    
    fields: fields

	});

  // Создаем массив с кнопками
  var buttons = [
    {
        extend: 'collection',
        autoClose: true,
        text: 'Экспорт',
        buttons: [
            { extend: 'copy', filename: 'Пассажиры' },
            { extend: 'excel', filename: 'Пассажиры' },
            { extend: 'csv', filename: 'Пассажиры' },
            { extend: 'pdf', filename: 'Пассажиры' },
            'print'
        ]
    },

    { extend: 'pageLength' }, 'colvis',
    // {
    //     text: 'Сохранить фильтр',
    //     action: function ( e, dt, node, config ) {

    //       var filterName = 'Passengers';
    //       var filters = {};
    //       // Сохраняем расположения колонок 
    //       filters[filterName]['colReorder'] = table.colReorder.order();

    //       // Сохраняем видимость колонок 
    //       filters[filterName]['columnVisibility'] = table.columns().visible().toArray();

    //       console.log(filters);
    //       toastr.success('Успешно сохранено!');
        
    //     }
    // }
  ];


  var columns = [
          // { data: 'passengers_id'},
          { data: 'fio'},
          { data: 'surname'},
          { data: 'name'},
          { data: 'pass'},
          { data: 'pas_type'},
          { data: 'benefit_doc'},
          { data: 'birth_date'},
          { data: 'gender'},
          { data: 'citizenship'},
          { data: 'contact'},
  ];



  if (role === 'superadmin') {
    buttons.unshift(
      { extend: 'create', editor: editor },
      { extend: 'edit', editor: editor },
      { extend: 'remove', editor: editor }
    );

    columns.unshift(
      { data: 'passengers_id'},
    );


  }


	var table = new DataTable('#passengers', {
        language: {
          url: '/js/datatables/i18n/ru.json',
          "processing": '<div class="custom-loader"><div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div></div></div>',
      },
        ajax: {
                url: '..//php/passengers.php',
                type: 'POST',
                data: function(d) {
                    d.key = myVar; // Передаем значение ключа на сервер
                    d.colum_name = colum_name;
                }
            },
        buttons: buttons,
		    processing: true,
        columns: columns,
        dom: 'QBfrtip',
        stateSave: true,
        select: true,
        serverSide: true,
        //deferRender: false,
        
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
        pageLength: 10,
        scrollY: true,
        scrollCollapse: true,
        scrollX: true
	} );

  
} );

}(jQuery));

