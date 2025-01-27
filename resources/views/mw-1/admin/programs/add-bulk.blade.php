<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<div class="modal fade" id="addSessionModalBulk" tabindex="-1" aria-labelledby="addSessionModalLabelBulk"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="addSessionModalLabelBulk">Please upload a correctly formatted spreadsheet</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="dataFormBulk" method="POST" action="{{ route('saveDataEmployeeInBulk') }}">
                @csrf

                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <b class="me-3">Ensure there are no other column with other information</b>
                        <button type="submit" id="uploadDataBtn" class="btn btn-primary mindway-btn-blue" hidden>Add
                            Bulk List of Employee</button>
                    </div>
                    <div id="preview-section">
                        <div class="table-responsive">
                            <table id="previewTable" class="table table-striped text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody">
                                    <tr>
                                        <td class="border-bottom-0 col-1">
                                            <h6 class="fw-normal mb-0">1</h6>
                                        </td>
                                        <td class="border-bottom-0 col-1">
                                            <h6 class="fw-normal mb-0">John Doe</h6>
                                        </td>
                                        <td class="border-bottom-0 col-1">
                                            <h6 class="fw-normal mb-0">john.doe@example.com</h6>
                                        </td>
                                        <td class="border-bottom-0 col-1">
                                            <h6 class="fw-normal mb-0">--</h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border-bottom-0 col-1">
                                            <h6 class="fw-normal mb-0">2</h6>
                                        </td>
                                        <td class="border-bottom-0 col-1">
                                            <h6 class="fw-normal mb-0">Jane Smith</h6>
                                        </td>
                                        <td class="border-bottom-0 col-1">
                                            <h6 class="fw-normal mb-0">jane.smith@example.com</h6>
                                        </td>
                                        <td class="border-bottom-0 col-1">
                                            <h6 class="fw-normal mb-0">--</h6>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br><hr><br>

                    <input type="hidden" name="programId" value="{{ $programId }}">
                    <input type="hidden" id="finalDataInput" name="finalData" value="">
                    <div class="mb-3">
                        <label for="uploadFile" class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="uploadFile" name="uploadFile" required>
                    </div>
                    <button type="button" id="previewDataBtn" class="btn btn-primary mindway-btn">Preview Data</button>

            </form>
        </div>
    </div>
</div>
</div>

<script>
    document.getElementById('previewDataBtn').addEventListener('click', function() {
        const fileInput = document.getElementById('uploadFile');
        const file = fileInput.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(event) {
                const data = new Uint8Array(event.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const sheetName = workbook.SheetNames[0];
                const sheetData = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {
                    header: 1
                });

                populatePreviewTable(sheetData);
            };
            reader.readAsArrayBuffer(file);
            const uploadDataBtn = document.getElementById('uploadDataBtn');
            uploadDataBtn.hidden = false;

        } else {
            alert('Please upload a file.');
        }
    });

    function populatePreviewTable(data) {
        const tableBody = document.getElementById('previewTableBody');
        tableBody.innerHTML = ''; // Clear existing rows

        data.slice(1).forEach((row, index) => {
            if (row.length > 1) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${row[0]}</td>
                    <td>${row[1]}</td>
                    <td><span><i class="ti ti-trash delete-row-btn"></i></span></td>`;

                // Add delete functionality
                tr.querySelector('.delete-row-btn').addEventListener('click', function() {
                    tr.remove();
                });

                tableBody.appendChild(tr);
            }
        });
    }

    document.getElementById('dataFormBulk').addEventListener('submit', function(event) {
        const tableBodyRows = document.querySelectorAll('#previewTableBody tr');
        const finalData = [];

        tableBodyRows.forEach(row => {
            const cells = row.children;
            finalData.push({
                id: cells[0].textContent.trim(),
                name: cells[1].textContent.trim(),
                email: cells[2].textContent.trim(),
            });
        });

        document.getElementById('finalDataInput').value = JSON.stringify(finalData);
 });
</script>