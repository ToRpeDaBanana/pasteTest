// $(document).ready(function() {
//     $('#paste_form_content').on('input change', function() {
//         const selectedLanguage = $('#paste_form_language').val();
//         const content = $(this).val();

//         // Обновляем элемент для отображения выделенного кода
//         let highlightedCode;

//         if (selectedLanguage !== 'none') {
//             highlightedCode = `<pre><code class="language-${selectedLanguage}">${Prism.util.encode(content)}</code></pre>`;
//         } else {
//             highlightedCode = `<pre><code>${Prism.util.encode(content)}</code></pre>`;
//         }

//         // Обновляем отображение
//         $('#highlightedCode').html(highlightedCode);
//         Prism.highlightAll(); // Применяем Prism.highlight
//     });

//     $('#paste_form_language').on('change', function() {
//         $('#paste_form_content').trigger('input'); // Обновляем выделение при смене языка
//     });
// });
// --------
// $(document).ready(function() {
//     $('#contentInput').on('input change', function() {
//         const selectedLanguage = $('#paste_form_language').val();
//         const content = $(this).val();

//         // Проверка языка и обновление выделения
//         let highlightedCode;
//         if (selectedLanguage !== 'none') {
//             highlightedCode = `<code class="language-${selectedLanguage}">${Prism.util.encode(content)}</code>`;
//         } else {
//             highlightedCode = `<code>${Prism.util.encode(content)}</code>`;
//         }

//         // Обновляем отображение
//         $('#highlightedCode').html(highlightedCode);
//         Prism.highlightAll(); // Применяем Prism.highlight
//     });

//     $('#paste_form_language').on('change', function() {
//         $('#contentInput').trigger('input'); // Обновляем выделение при смене языка
//     });
// });
// ------------
// $(document).ready(function() {
//     $('#paste_form_content').on('input change', function() {
//         const selectedLanguage = $('#paste_form_language').val();
//         const content = $(this).val();

//         // Обновляем элемент для отображения выделенного кода
//         let highlightedCode;

//         if (selectedLanguage !== 'none') {
//             highlightedCode = `<pre><code class="language-${selectedLanguage}">${Prism.util.encode(content)}</code></pre>`;
//         } else {
//             highlightedCode = `<pre><code>${Prism.util.encode(content)}</code></pre>`;
//         }

//         // Обновляем отображение
//         $('#highlightedCode').html(highlightedCode);
//         Prism.highlightAll(); // Применяем Prism.highlight
//     });

//     $('#paste_form_language').on('change', function() {
//         $('#paste_form_content').trigger('input'); // Обновляем выделение при смене языка
//     });
// });

// $(document).ready(function() {
//     $('#paste_form_content').on('input change', function() {
//         const selectedLanguage = $('#paste_form_language').val();
//         const content = $(this).val();

//         // Обновляем элемент для отображения выделенного кода
//         let highlightedCode;

//         if (selectedLanguage !== 'none') {
//             highlightedCode = `<pre><code class="language-${selectedLanguage}">${Prism.util.encode(content)}</code></pre>`;
//         } else {
//             highlightedCode = `<pre><code>${Prism.util.encode(content)}</code></pre>`;
//         }

//         // Обновляем отображение
//         $('#highlightedCode').html(highlightedCode);
//         Prism.highlightAll(); // Применяем Prism.highlight
//     });

//     $('#paste_form_language').on('change', function() {
//         $('#paste_form_content').trigger('input'); // Обновляем выделение при смене языка
//     });
// });
$(document).ready(function() {
    function updateHighlightedCode() {
        const selectedLanguage = $('#paste_form_language').val();
        const content = $('#paste_form_content').val();

        let highlightedCode;

        if (selectedLanguage !== 'none' && selectedLanguage) {
            highlightedCode = `<pre><code class="language-${selectedLanguage}">${Prism.util.encode(content)}</code></pre>`;
        } else {
            // Здесь можно использовать класс по умолчанию для текстов без языка
            highlightedCode = `<pre><code class="language-default">${Prism.util.encode(content)}</code></pre>`;
        }

        $('#highlightedCode').html(highlightedCode);
        Prism.highlightAll(); // Применяем Prism.highlight
    }

    $('#paste_form_content').on('input change', updateHighlightedCode);
    $('#paste_form_language').on('change', function() {
        updateHighlightedCode();
    });

    // Инициализация выделенного текста при загрузке страницы
    updateHighlightedCode();
    
});

