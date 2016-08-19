$(document).ready(function () {
    $('#dbFiles').change(function () {
        var html = '<ul class="mergeItems">', k = 0;
        $('#dbFiles option:selected').each(function () {
            k = (k + 1);
            html += "<li><input type='checkbox' name='mitems[]' id='id_" + k + "' class='mitems' value='" + $(this).val() + "' data-file='" + $(this).val() + "'>";
            html += "<label for='id_" + k + "'>" + $(this).val() + "</label></li>";
        });
        html += "</ul>";
        if(k>1)
            $('#dbMerge').html(html);
        else
            $('#dbMerge').html('Выбирете минимум 2 базы');
        if (k > 0) {
            $('#gobot').css('display', 'block');
        } else {
            $('#gobot').css('display', 'none');
        }
    });
    $('#gogo').on('click', function () {
        var html = '<p>Началась загрузка ...</p>', k = 0, file = '', merge = '';
        $('#dbFiles option:selected').each(function () {
            k = (k + 1);
            file += $(this).val() + '|';
        });
        $('.mergeItems input:checked').each(function () {
            merge += $(this).val() + '|';
        });

        if (k > 0) {
            $('#dbResult').html(html);
            $.post(
                "/index.php",
                {
                    ajax: 'Y',
                    action: 'importTestDB',
                    file: file,
                    merge: merge
                }, function (data) {
                    if (data.status == "ok") {
                        var arr =  $.map(data.files, function(value, index) {
                            return [value];
                        });
                        var html = "<h2>Сконвертированнные файлы</h2><ul class='out_files'>";
                        for (var i = 0; i < arr.length; i++) {
                            html += '<li><a href="'+arr[i].filexml+'" download>'+arr[i].file+'[xml]</a></li>';
                        }
                        html += "</ul>";
                        $('#dbResult').html(html);
                    } else {

                    }
                }, 'json');
        }
    });

});