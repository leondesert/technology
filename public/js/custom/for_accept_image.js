document.addEventListener('DOMContentLoaded', function () {
    const receiptPhotoInput = document.getElementById('receipt_photo');

    if (receiptPhotoInput) {
        receiptPhotoInput.addEventListener('change', function(event) {
            const fileInput = event.target;
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const fileName = file.name;
                const validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                const fileExtension = fileName.split('.').pop().toLowerCase();

                if (!validExtensions.includes(fileExtension)) {
                    alert('Неверное расширение файла! Допустимы только JPG, JPEG, PNG, PDF.');
                    // Очищаем значение поля, чтобы предотвратить отправку неверного файла
                    fileInput.value = ''; 
                    // Для Bootstrap 4/5 custom-file-input, нужно также сбросить метку
                    // Предполагаем, что метка является следующим элементом с классом .custom-file-label
                    const label = fileInput.nextElementSibling;
                    if (label && label.classList.contains('custom-file-label')) {
                        label.textContent = 'Выберите файл';
                    }
                }
            }
        });
    }
});