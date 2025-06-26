<script>
  let configFields = [];
  let editAddersSelect = $('#edit_adders_wrapper');

  // Thumbnail & Detail Photo Previews
  window.previewThumbnail = function (input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => $('#edit_thumbnail_preview').attr('src', e.target.result);
      reader.readAsDataURL(input.files[0]);
    }
  }

  window.previewDetailPhoto = function (input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => $('#edit_detail_photo_preview').attr('src', e.target.result);
      reader.readAsDataURL(input.files[0]);
    }
  }

  // Adder Rows
  function addAdderRow(name = '', price = '', type = 1, minQty = 1, maxQty = 1) {
  const html = `
    <div class="adder-row-wrapper mb-3">
      <div class="row align-items-center adder-row">
        <div class="col-md-3">
          <input type="text" class="form-control" name="adders_names[]" placeholder="Adder Name" value="${name}">
        </div>
        <div class="col-md-2">
          <input type="number" step="0.01" class="form-control" name="adders_prices[]" placeholder="Price" value="${price}">
        </div>
        <div class="col-md-2">
          <select class="form-control" name="adders_types[]">
            <option value="1" ${type === 1 ? 'selected' : ''}>Linear</option>
            <option value="2" ${type === 2 ? 'selected' : ''}>Dynamic</option>
          </select>
        </div>
        <div class="col-md-2">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">Min</span>
            </div>
            <input type="number" class="form-control" name="adders_min_qty[]" value="${minQty}" min="1">
          </div>
        </div>
        <div class="col-md-2">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">Max</span>
            </div>
            <input type="number" class="form-control" name="adders_max_qty[]" value="${maxQty}" min="1">
          </div>
        </div>
        <div class="col-md-1">
          <button type="button" class="btn btn-primary btn-sm btn-remove-adder">X</button>
        </div>
      </div>
    </div>`;
  editAddersSelect.append(html);
}


  $(document).on('click', '#btn-add-adder', () => addAdderRow());
  $(document).on('click', '.btn-remove-adder', function () {
    $(this).closest('.adder-row').remove();
  });

  // Add Config Field (Select / Dynamic Select)
  function addConfigField(field, pricing) {
    if (!field || (field.type !== 'select' && field.type !== 'dynamic_select')) return;
    console.log(field);
    const index = $('#configuration_fields_container .config-field').length;
    let html = `
      <div class="card p-3 mb-3 config-field">
        <div class="mb-2"><strong>${field.label}</strong> (${field.type})</div>
        <div class="options-section" id="options_section_${index}">
          ${field.pricing === 'true' ? renderOptionsWithPricing(field.options, index, pricing) : renderOptionsWithoutPricing(field.options, index)}
        </div>
      </div>`;

    $('#configuration_fields_container').append(html);
    $(`#options_wrapper_${index} .option-row input[type="text"]`).each(function () {
  updatePricingInputName($(this));
});
  }

  function renderOptionsWithoutPricing(options = [], index) {
    let html = `<label>Options</label><div class="options-wrapper" id="options_wrapper_${index}">`;
    options.forEach(option => {
      html += optionRow(index, option);
    });
    html += `</div><button type="button" class="btn btn-primary btn-sm mt-2 btn-add-option" data-index="${index}">Add Option</button>`;
    return html;
  }

  function renderOptionsWithPricing(options = [], index, pricing = {}) {
    let html = `<label>Options & Pricing</label><div class="options-wrapper" id="options_wrapper_${index}">`;
    options.forEach(option => {
      const priceValue = getPriceValue(option, pricing);
      html += optionRow(index, option, priceValue);
    });
    html += `</div><button type="button" class="btn btn-primary btn-sm mt-2 btn-add-option" data-index="${index}">Add Option</button>`;
    return html;
  }

  function getPriceValue(option, pricing) {
    if (!pricing) return '';
    if (pricing[option]) return pricing[option].price_per_sqft;
    if (pricing.battery && pricing.battery[option]) return pricing.battery[option];

    for (const key in pricing) {
      const p = pricing[key];
      if (p.capacity) {
        const match = p.capacity.find(cap => cap.label === option);
        if (match) return match.price;
      }
      if (p['R-Value']) {
        const match = p['R-Value'].find(val => val.label === option);
        if (match) return match.price;
      }
    }
    return '';
  }

function optionRow(index, value = '', price = null, subCat = null, withPricing = false) {
  const uniqueRowId = Date.now() + Math.floor(Math.random() * 1000);
  const pricingField = withPricing
  ? `<input type="number" class="form-control pricing-input" step="0.01" data-index="${index}" data-subcat="${subCat ?? ''}" data-old-name="__option__" value="${price ?? ''}" placeholder="Price">`
  : '';
  return `<div class="input-group mb-1 option-row">
    <input type="text" class="form-control ${withPricing ? '' : 'parent-option-input'}" data-row-id="${uniqueRowId}" data-index="${index}" name="config_fields[${index}][options]${subCat ? `[${subCat}]` : ''}[]" value="${value}" placeholder="Option Label">
    ${pricingField}
    <button type="button" class="btn btn-primary btn-sm btn-remove-option">X</button>
  </div>`;
}
function renderOptionsWithPricing(options = [], index, pricing = {}) {
  let html = `<label>Options & Pricing</label><div class="options-wrapper" id="options_wrapper_${index}">`;
  options.forEach(option => {
    const priceValue = getPriceValue(option, pricing);
    html += optionRow(index, option, priceValue, null, true);
  });
  html += `</div><button type="button" class="btn btn-primary btn-sm mt-2 btn-add-option" data-index="${index}" data-pricing="true">Add Option</button>`;
  return html;
}
function updatePricingInputName(optionInput) {
  const label = optionInput.val().trim();
  const index = optionInput.data('index');
  const optionRow = optionInput.closest('.option-row');
  const pricingInput = optionRow.find('.pricing-input');
  const subCat = pricingInput.data('subcat');

  if (pricingInput.length) {
    if (!label) {
      pricingInput.prop('disabled', true).val('');
    } else {
      pricingInput.prop('disabled', false);
      const nameAttr = `config_fields[${index}][pricing]${subCat ? `[${subCat}]` : ''}[${label}]`;
      pricingInput.attr('name', nameAttr);
    }
  }
}
$(document).on('blur', '.parent-option-input, .option-row input[type="text"]', function () {
  updatePricingInputName($(this));
});
  // Dynamic Options for dependent selects
  function renderDynamicOptionsHTML(field, index, pricing = {}, subCategory = null) {
    const withPricing = field.pricing === 'true';
    const subCatOptions = field.options[subCategory] || [];
    let html = `<label>${field.label} - ${subCategory}</label>
      <div class="options-wrapper" id="options_wrapper_${index}_${subCategory.replace(/\s+/g, '_')}">`;

    if (subCatOptions.length === 0) {
      html += `<div class="alert alert-warning p-2">No options found for ${subCategory}. Please add at least one option.</div>`;
    }
    subCatOptions.forEach(option => {
      const priceValue = pricing[subCategory]?.[option] ?? '';
      const hasPrice = !!priceValue;
      html += optionRow(index, option, priceValue, subCategory,hasPrice);
    });
    html += `</div><button type="button" class="btn btn-primary btn-sm mt-2 btn-add-dynamic-option" data-pricing="${withPricing}" data-index="${index}" data-subcategory="${subCategory}">Add Option</button>`;
    setTimeout(() => {
  $(`#options_wrapper_${index}_${subCategory.replace(/\s+/g, '_')} .option-row input[type="text"]`).each(function () {
    updatePricingInputName($(this));
  });
}, 0);
    return html;
  }

$(document).on('click', '.btn-add-option', function () {
  const index = $(this).data('index');
  const withPricing = $(this).data('pricing') === true || $(this).data('pricing') === "true";
  $(`#options_wrapper_${index}`).append(optionRow(index, '', null, null, withPricing));
});

  $(document).on('click', '.btn-add-dynamic-option', function () {
    const index = $(this).data('index');
    const subCat = $(this).data('subcategory');
    const withPricing = $(this).data('pricing') === true || $(this).data('pricing') === "true";
    const wrapperId = `#options_wrapper_${index}_${subCat.replace(/\s+/g, '_')}`;
    $(wrapperId).append(optionRow(index, '', '', subCat,withPricing));
  });

  $(document).on('click', '.btn-remove-option', function () {
    $(this).closest('.option-row').remove();
  });

$(document).on('blur', '.parent-option-input', function () {
  const label = $(this).val().trim();
  const index = $(this).data('index');
  const rowId = $(this).data('row-id');
  console.log(index);
  console.log(rowId);
  console.log(label);
  if (!label) return;

  const parentField = configFields.fields[index];
  if (!parentField) return;

  configFields.fields.forEach((f, depIndex) => {
    if (f.type === 'dynamic_select' && f.depends_on === parentField.name) {
      const sectionId = `dynamic_section_${depIndex}_${rowId}`;
      console.log(depIndex);
      console.log(sectionId);
      // If section exists → update title
      if ($(`#${sectionId}`).length) {
        $(`#${sectionId} h6`).text(label);
        return;
      }

      // Else, create new section
      const dynamicHtml = `
        <div id="${sectionId}" class="mt-3">
          <h6>${label}</h6>
          ${renderDynamicOptionsHTML(f, depIndex, {}, label)}
        </div>
      `;
      $('#configuration_fields_container').append(dynamicHtml);
    }
  });
});



  // Edit Category Load
  $(document).on('click', '.btn-edit-category', function () {
    $('#loader').show();
    const categoryId = $(this).data('id');
    $.ajax({
      url: `/admin/category/${categoryId}`,
      type: 'GET',
      success: res => {
         $('#loader').hide();
        const category = res.category;
        $('#edit-category-form')[0].reset();
        $('#edit_category_id').val(category.id);
        $('#edit_name').val(category.name);
        $('#edit_thumbnail_preview').attr('src', category.thumbnail_url);
        $('#edit_detail_photo_preview').attr('src', category.detail_photo_url);
        editAddersSelect.empty();
        category.adders.forEach(a => addAdderRow(a.name, a.price, a.type,a.min_qty,a.max_qty));
        configFields = JSON.parse(category.configuration);
        const pricing = JSON.parse(category.pricing);
        $('#configuration_fields_container').empty();

        let index = 0;
        configFields.fields.forEach(field => {
          if (field.type === 'dynamic_select') {
            Object.keys(field.options).forEach(subCat => {
              const parentInput = $(`#configuration_fields_container .parent-option-input`).filter(function() {
        return $(this).val() === subCat;
      });
      const parentRowId = parentInput.data('row-id');
              $('#configuration_fields_container').append(`<div id="dynamic_section_${index}_${parentRowId}" class="mt-3"><h6 class="mt-3">${subCat}</h6>${renderDynamicOptionsHTML(field, index, pricing, subCat)}</div>`);
            });
            index++;
          } else {
            addConfigField(field, pricing);
            index++;
          }
        });
        $('#editCategoryModal').modal({backdrop: 'static',keyboard: false}).modal('show');
      }
    });
  });

  // Submit form
  $('#edit-category-form').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: "{{ route('admin.category.update') }}",
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: res => {
        toastr.success(res.message);
        $('#editCategoryModal').modal('hide');
        category_table.ajax.reload(null, false);
      },
      error: xhr => toastr.error(xhr.responseJSON.message)
    });
  });
</script>