<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<style>
#preview-table {
  position: relative;
}

#preview-table td, #preview-table th {
  max-width: 250px;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  position: relative;
}

#preview-table td:hover::after {
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
  box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

</style>
<h3>📂 Import MCQs from Excel</h3>

<div class="mb-3">
    <input type="file" id="excel-file" accept=".xlsx, .xls" class="form-control">
</div>

<div id="preview-container" class="table-responsive" style="display: none;">
    <table id="preview-table" class="table table-bordered table-striped">
        <thead></thead>
        <tbody></tbody>
    </table>

    <button id="submit-mcqs" class="btn btn-primary mt-3">✅ Submit MCQs</button>
</div>

<!-- SheetJS + jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let mcqData = [];

    $('#excel-file').on('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            const data = new Uint8Array(event.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheet = workbook.Sheets[workbook.SheetNames[0]];
            const json = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            if (json.length < 2) {
                alert('Invalid file format.');
                return;
            }

            const headers = json[0];
            const rows = json.slice(1);
            mcqData = [];

            const $thead = $('#preview-table thead').empty();
            const $tbody = $('#preview-table tbody').empty();

            const $headerRow = $('<tr>');
            headers.forEach(h => $headerRow.append($('<th>').text(h)));
            $thead.append($headerRow);

            rows.forEach((row, i) => {
                const mcq = {};
                const $tr = $('<tr>');
                headers.forEach((col, j) => {
                    let fullValue = row[j] || '';
                    let displayValue = fullValue.length > 100 ? fullValue.slice(0, 100) + '…' : fullValue;
                    mcq[col] = row[j] || '';
                    $tr.append($('<td>')
                        .text(displayValue)
                        .attr('data-full', fullValue));
                });
                mcqData.push(mcq);
                $tbody.append($tr);
            });

            $('#preview-container').show();
        };

        reader.readAsArrayBuffer(file);
    });

    $('#submit-mcqs').on('click', function () {
        if (mcqData.length === 0) {
            alert('No data to submit.');
            return;
        }

        $.ajax({
            type: 'POST',
            url: '<?= Url::to(['mcq/import']) ?>',
            data: JSON.stringify({ mcqs: mcqData }),
            contentType: 'application/json',
            success: function (response) {
                alert(response.message || 'Import successful');
            },
            error: function (xhr) {
                alert('Import failed: ' + xhr.responseText);
            }
        });
    });
</script>