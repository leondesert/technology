 		// Если выбрано "Наличная оплата"
        document.getElementById('method').addEventListener('change', function() {
            var selectedValue = this.value;
            
            
            if (selectedValue === 'Наличная оплата') {
                document.getElementById('bank_block').style.display = 'none';
                document.getElementById('bank').removeAttribute('required');


            } else {
                document.getElementById('bank_block').style.display = 'block';
                document.getElementById('bank').setAttribute('required', 'required');
            }
        });

        // При загрузке страницы проверяем текущее значение метода оплаты
        window.onload = function() {
            var selectedValue = document.getElementById('method').value;
            if (selectedValue === 'Наличная оплата') {
                document.getElementById('bank_block').style.display = 'none';
            }
        };