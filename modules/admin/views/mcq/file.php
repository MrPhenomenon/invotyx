<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<style>
    #preview-table {
        position: relative;
        border-collapse: collapse;
    }

    #preview-table td,
    #preview-table th {
        max-width: 250px;
        position: relative;
        border: 1px solid #ccc;
        padding: 5px;
        vertical-align: top;
    }

    #preview-table td .cell-content,
    #preview-table th .cell-content {
        display: block;
        width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #preview-table td:hover::after,
    #preview-table th:hover::after {
        content: attr(data-full);
        position: absolute;
        background: rgba(0, 0, 0, 0.85);
        color: #fff;
        padding: 6px 10px;
        border-radius: 4px;
        white-space: normal;
        z-index: 9999;
        top: 100%;
        left: 0;
        width: max-content;
        max-width: 400px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        margin-top: 5px;
        pointer-events: none;
    }
</style>
<h3>📂 Import MCQs from Excel</h3>

<form id="uploadForm" enctype="multipart/form-data">
<div class="mb-3 row">
        <div class="col-12" id="warning">
            <div class="alert alert-info">
                <i class="bi bi-info-square-fill"></i> For best performance, we recommend importing a maximum of
                <strong>1000 rows</strong> per file.
            </div>
        </div>
        <div class="col-10">
            <input type="file" id="excel-file" accept=".xlsx, .xls" class="form-control" name="excelFile">
        </div>
        <div class="col-2">
            <button id="submit-mcqs" class="btn btn-success d-none h-100">Submit MCQs</button>
        </div>
    </div>
</form>

<div id="preview-container" class="table-responsive" style="display: none;">
    <table id="preview-table" class="table table-bordered table-striped">
        <thead></thead>
        <tbody></tbody>
    </table>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<?php
$js = <<<JS
    let mcqData = [];
    
    $('#excel-file').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) {
        hideloader();n
        return;
    }

    $('#submit-mcqs').removeClass('d-none');
    showloader();

    setTimeout(() => {
        const reader = new FileReader();

        reader.onload = function (event) {
            try {
                const data = new Uint8Array(event.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const sheet = workbook.Sheets[workbook.SheetNames[0]];
                const json = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                const cleaned = json.filter(row => 
                  Array.isArray(row) && row.some(cell => cell && String(cell).trim() !== '')
                );

                if (json.length < 2) {
                    hideloader();
                    alert('Invalid file format: Not enough data or missing headers.');
                    return;
                }
                if (cleaned.length > 1000) {
                     hideloader();
                     $('#warning').html(`
                         <div class="alert alert-danger">
                             <i class="bi bi-exclamation-triangle-fill"></i> 
                             Uploaded file has more than 1000 rows, it may fail to submit.
                         </div>
                     `);
                    } else {
                        $('#warning').html(``);
                    }

                const headers = cleaned[0];
                if (!headers || headers.some(h => typeof h === 'undefined' || h === null || String(h).trim() === '')) {
                    alert('Invalid file format: Headers are missing or invalid.');
                    return;
                }
                const rows = cleaned.slice(1);

                const thead = $('#preview-table thead').empty();
                const tbody = $('#preview-table tbody').empty();

                const headerRow = $('<tr>');
                headers.forEach(h => headerRow.append($('<th>').text(String(h))));
                thead.append(headerRow);

                rows.forEach((row, i) => {
                    const mcq = {};
                    const tr = $('<tr>');
                    headers.forEach((col, j) => {
                        let fullValue = (row && typeof row[j] !== 'undefined' && row[j] !== null) ? String(row[j]) : '';
                        mcq[String(col)] = fullValue;

                        const td = $('<td>').attr('data-full', fullValue);
                        const cellContentDiv = $('<div>').addClass('cell-content').text(fullValue);
                        td.append(cellContentDiv);
                        tr.append(td);
                    });
                    mcqData.push(mcq);
                    tbody.append(tr);
                });

                $('#preview-container').show();

            } catch (err) {
                console.error("Error processing Excel file:", err);
                alert('An error occurred while processing the Excel file. Please check the file format and content.');
            } finally {
                hideloader();
            }
        };

        reader.onerror = function (error) {
            console.error("FileReader error:", error);
            alert('Error reading file.');
            hideloader(); 
        };

        reader.readAsArrayBuffer(file);

    }, 400);
});

    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: '/admin/mcq/save-file',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                res.success ? showToast(res.message, 'success') : showToast(res.message, 'danger');
            },
            error: function (xhr) {
                alert('Import failed: ' + xhr.responseText);
            }
        });
    });
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>