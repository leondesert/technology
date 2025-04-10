$(document).ready(function() {
  // Получаем текущий URL страницы
  var currentLocation = window.location.pathname;

  // Находим все ссылки меню
  var menuLinks = $('.nav-link');

  // Проходимся по каждой ссылке и сравниваем URL с текущим URL страницы
  menuLinks.each(function() {
    var link = $(this).attr('href');

    // Проверяем, совпадает ли текущий URL со ссылкой меню
    if (currentLocation.includes(link)) {
      // Добавляем класс "active" к текущей ссылке
      $(this).addClass('active');

      // Находим ближайший родительский элемент с классом "nav-item"
      var parentNavItem = $(this).closest('.nav-item');
      // Находим родительский элемент уровнем выше
      var parentNavTreeview = parentNavItem.closest('.nav-treeview');

      // Если родитель уровнем выше найден, добавляем класс "menu-open"
      if (parentNavTreeview.length > 0) {
        var elementNavItem = parentNavTreeview.parent();
        elementNavItem.addClass('menu-open');
        elementNavItem.children('.nav-link').first().addClass('active');
      }
      
    }
  });
});