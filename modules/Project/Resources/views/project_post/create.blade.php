@extends('backend.layouts.app')
@section('title', localize('projects'))
@push('css')
    <link href="{{ asset('backend/assets/plugins/Bootstrap-5-Tag-Input/tagsinput.css') }}" rel="stylesheet">
@endpush
@section('content')
    @include('backend.layouts.common.validation')
    @include('backend.layouts.common.message')

    <form id="projectForm" action="{{ route('project.post.create')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fs-17 fw-semi-bold mb-0">Project Post</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Select Project</label>
                        <select id="project_select" name="project_id" class="form-select select-basic-single">
                            <option value="">--Select --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" data-type="{{ $project->project_type }}">
                                    {{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="typeSpecificArea" class="card mb-3 d-none">
                    <div class="card-header">Type Specific Details</div>
                    <div class="card-body" id="typeFieldsContainer"></div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Submit Project Post</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('js')
    <script>
        const schemas = {
            mosque: [{
                    key: 'construction_stage',
                    label: 'Construction Stage',
                    type: 'text'
                },
                {
                    key: 'estimated_cost',
                    label: 'Estimated Cost',
                    type: 'number'
                },
                {
                    key: 'land_area',
                    label: 'Land Area',
                    type: 'text'
                },
                {
                    key: 'main_materials',
                    label: 'Main Materials',
                    type: 'repeater',
                    item_schema: [{
                            key: 'name',
                            label: 'Item',
                            type: 'text'
                        },
                        {
                            key: 'qty',
                            label: 'Qty',
                            type: 'number'
                        },
                        {
                            key: 'unit',
                            label: 'Unit',
                            type: 'text'
                        },
                        {
                            key: 'unit_price',
                            label: 'Unit Price',
                            type: 'number'
                        }
                    ]
                },
                {
                    key: 'architect_contact',
                    label: 'Architect Contact',
                    type: 'text'
                }
            ],
            orphan: [{
                    key: 'child_name',
                    label: 'Child Name',
                    type: 'text'
                },
                {
                    key: 'dob',
                    label: 'Date of Birth',
                    type: 'date'
                },
                {
                    key: 'gender',
                    label: 'Gender',
                    type: 'select',
                    options: ['Male', 'Female', 'Other']
                },
                {
                    key: 'guardian_contact',
                    label: 'Guardian Contact',
                    type: 'text'
                },
                {
                    key: 'education_status',
                    label: 'Class / Education Status',
                    type: 'text'
                },
                {
                    key: 'medical_needs',
                    label: 'Medical Needs',
                    type: 'textarea'
                },
                {
                    key: 'monthly_support_required',
                    label: 'Monthly Support Required',
                    type: 'number'
                },
                {
                    key: 'image',
                    label: 'Image',
                    type: 'file'
                }
            ],
            scholarship: [{
                    key: 'student_name',
                    label: 'Student Name',
                    type: 'text'
                },
                {
                    key: 'university',
                    label: 'University',
                    type: 'text'
                },
                {
                    key: 'program',
                    label: 'Program',
                    type: 'text'
                },
                {
                    key: 'year',
                    label: 'Year',
                    type: 'number'
                },
                {
                    key: 'gpa',
                    label: 'GPA',
                    type: 'number',
                    step: '0.01'
                },
                {
                    key: 'documents',
                    label: 'Documents (URLs comma separated)',
                    type: 'text'
                },
                {
                    key: 'requested_amount',
                    label: 'Requested Amount',
                    type: 'number'
                }
            ],
            rehab: [{
                    key: 'family_head_name',
                    label: 'Family Head Name',
                    type: 'text'
                },
                {
                    key: 'members_count',
                    label: 'Members Count',
                    type: 'number'
                },
                {
                    key: 'vulnerabilities',
                    label: 'Vulnerabilities (comma separated)',
                    type: 'text'
                },
                {
                    key: 'monthly_expenses',
                    label: 'Monthly Expenses',
                    type: 'number'
                },
                {
                    key: 'preferred_assistance',
                    label: 'Preferred Assistance (comma)',
                    type: 'text'
                }
            ]
        };

        function escapeHtml(s) {
            if (s === null || s === undefined) return '';
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function clearTypeArea() {
            $('#typeSpecificArea').addClass('d-none');
            $('#typeFieldsContainer').empty();
            console.log('clearTypeArea');
        }

        function showTypeArea() {
            $('#typeSpecificArea').removeClass('d-none');
            console.log('showTypeArea');
        }

        function addRepeaterRow(rid, itemSchema, values = {}, idx = null) {
            idx = (idx !== null) ? idx : $(`#${rid} .repeater-row`).length;
            const $row = $('<div class="repeater-row border rounded p-2 mb-2"></div>');
            let html = '<div class="row g-2">';
            itemSchema.forEach(item => {
                const val = values[item.key] ?? '';
                html +=
                    `<div class="col-md-3"><label class="form-label">${item.label}</label><input class="form-control" name="detail[${rid}][${idx}][${item.key}]" type="${item.type === 'number' ? 'number' : 'text'}" value="${escapeHtml(val)}"></div>`;
            });
            html +=
                `</div><div class="text-end mt-2"><button type="button" class="btn btn-sm btn-outline-danger remove-row">Remove</button></div>`;
            $row.html(html);
            $(`#${rid}`).append($row);
            console.log('addRepeaterRow', rid, idx);
        }

        function renderMultiRowTable(typeSlug, detailArray = []) {
            const schema = schemas[typeSlug];
            if (!schema) return;
            showTypeArea();
            const $c = $('#typeFieldsContainer').empty();
            const tableId = `table_${typeSlug}`;
            let html =
                `<div class="mb-3"><div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">${typeSlug.charAt(0).toUpperCase()+typeSlug.slice(1)} Entries</h6><button type="button" class="btn btn-sm btn-outline-primary" id="add_row_${typeSlug}">Add More</button></div><div class="table-responsive"><table class="table table-sm table-bordered" id="${tableId}"><thead><tr>`;
            schema.forEach(col => {
                html += `<th>${col.label}</th>`;
            });
            html += `<th style="width:110px">Actions</th></tr></thead><tbody></tbody></table></div></div>`;
            $c.append(html);
            const $tbody = $(`#${tableId} tbody`);

            function addRow(values = {}, idx = null) {
                idx = (idx !== null) ? idx : $tbody.find('tr').length;
                let row = '<tr>';
                schema.forEach(col => {
                    const val = values[col.key] ?? '';
                    if (col.type === 'textarea') {
                        row +=
                            `<td><textarea class="form-control form-control-sm" name="detail[${typeSlug}][${idx}][${col.key}]" rows="2">${escapeHtml(val)}</textarea></td>`;
                    } else if (col.type === 'select') {
                        row +=
                            `<td><select class="form-control form-control-sm" name="detail[${typeSlug}][${idx}][${col.key}]">`;
                        col.options.forEach(opt => {
                            const sel = (opt == val) ? 'selected' : '';
                            row += `<option ${sel}>${escapeHtml(opt)}</option>`;
                        });
                        row += `</select></td>`;
                    } else if (col.type === 'file') {
                        row +=
                            `<td><input class="form-control form-control-sm" name="detail[${typeSlug}][${idx}][${col.key}]" type="file" accept="image/*"></td>`;
                    } else {
                        const stepAttr = col.step ? ` step="${col.step}"` : '';
                        row +=
                            `<td><input class="form-control form-control-sm" name="detail[${typeSlug}][${idx}][${col.key}]" type="${col.type==='number'?'number':'text'}"${stepAttr} value="${escapeHtml(val)}"></td>`;
                    }
                });
                row +=
                    `<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-entry">Remove</button></td></tr>`;
                $tbody.append(row);
            }
            if (Array.isArray(detailArray) && detailArray.length) {
                detailArray.forEach((r, i) => addRow(r, i));
            } else {
                addRow({}, 0);
            }
            $(`#add_row_${typeSlug}`).off('click').on('click', function() {
                addRow({}, null);
                reindexRows(typeSlug);
            });
            $tbody.off('click').on('click', '.remove-entry', function() {
                $(this).closest('tr').remove();
                reindexRows(typeSlug);
            });
            console.log('renderMultiRowTable', typeSlug);
        }

        function reindexRows(typeSlug) {
            const $tbody = $(`#table_${typeSlug} tbody`);
            $tbody.find('tr').each(function(index) {
                $(this).find('input, textarea, select').each(function() {
                    const name = $(this).attr('name');
                    const m = name.match(/^detail\[(.+?)\]\[\d+\]\[(.+?)\]$/);
                    if (m) {
                        const root = m[1];
                        const key = m[2];
                        const newName = `detail[${root}][${index}][${key}]`;
                        $(this).attr('name', newName);
                    }
                });
            });
            console.log('reindexRows', typeSlug);
        }

        function renderTypeFields(typeSlug, detailData = {}) {
            if (!typeSlug) {
                clearTypeArea();
                return;
            }
            const normalized = String(typeSlug).toLowerCase();
            if (normalized === 'mosque') {
                showTypeArea();
                const schema = schemas.mosque;
                const $c = $('#typeFieldsContainer').empty();
                schema.forEach(field => {
                    if (field.type === 'repeater') {
                        const rid = `repeater_${field.key}`;
                        $c.append(
                            `<div class="mb-3"><label class="form-label">${field.label}</label><div id="${rid}"></div><button type="button" class="btn btn-sm btn-outline-primary mt-2" data-repeater="${rid}">Add</button></div>`
                            );
                        const existing = Array.isArray(detailData[field.key]) ? detailData[field.key] : [];
                        existing.forEach((row, idx) => addRepeaterRow(rid, field.item_schema, row, idx));
                        $c.on('click', `button[data-repeater="${rid}"]`, function() {
                            addRepeaterRow(rid, field.item_schema, {}, $(`#${rid} .repeater-row`).length);
                        });
                        $c.on('click', `#${rid} .remove-row`, function() {
                            $(this).closest('.repeater-row').remove();
                        });
                    } else {
                        const val = detailData[field.key] ?? '';
                        const name = `detail[${field.key}]`;
                        if (field.type === 'textarea') {
                            $c.append(
                                `<div class="mb-3"><label class="form-label">${field.label}</label><textarea name="${name}" class="form-control" rows="2">${escapeHtml(val)}</textarea></div>`
                                );
                        } else {
                            const stepAttr = field.step ? ` step="${field.step}"` : '';
                            $c.append(
                                `<div class="mb-3"><label class="form-label">${field.label}</label><input name="${name}" class="form-control" type="${field.type === 'number' ? 'number' : 'text'}"${stepAttr} value="${escapeHtml(val)}"></div>`
                                );
                        }
                    }
                });
                console.log('renderTypeFields mosque');
            } else if (normalized === 'orphan' || normalized === 'scholarship' || normalized === 'rehab') {
                let arr = [];
                if (Array.isArray(detailData)) arr = detailData;
                else if (Array.isArray(detailData[normalized])) arr = detailData[normalized];
                else if (Array.isArray(detailData.entries)) arr = detailData.entries;
                renderMultiRowTable(normalized, arr);
                console.log('renderTypeFields multi', normalized, arr);
            } else {
                clearTypeArea();
            }
        }

        function mapTypeStringToSlug(typeString) {
            if (!typeString) return '';
            const s = String(typeString).toLowerCase();
            if (s.includes('mosque')) return 'mosque';
            if (s.includes('orphan')) return 'orphan';
            if (s.includes('scholar')) return 'scholarship';
            if (s.includes('rehab') || s.includes('rehabil')) return 'rehab';
            return s.replace(/\s+/g, '_');
        }

        $(function() {
            const PRESET = window.PRESET_DETAILS || (window.PRESET_DETAIL ? {
                [$('#project_select').val() || 'new']: window.PRESET_DETAIL
            } : {});
            $('#project_select').on('change', function() {
                const $opt = $(this).find('option:selected');
                const rawType = $opt.data('type') || '';
                const type = mapTypeStringToSlug(rawType);
                const pid = $opt.val();
                const detailData = (pid && PRESET[pid]) ? PRESET[pid] : {};
                renderTypeFields(type, detailData);
            });
            const preSel = $('#project_select').val();
            if (preSel) {
                $('#project_select').trigger('change');
            }
        });
    </script>
@endpush
