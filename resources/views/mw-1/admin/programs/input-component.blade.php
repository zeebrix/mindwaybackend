 <div class="col-12 me-3" style="{{ $css }}" id="{{ $id }}">
     <div class="card" style="border-radius: 20px">
         <div style="height:55px;padding:10px 0px 5px 20px !important"
             class="card-body d-flex flex-column justify-content-center p-4">
             <label for="{{ $name }}Id" class="form-label" style="margin:unset;font-weight:400;">{{ $label }}</label>
             <div class="d-flex justify-content-between align-items-center mb-4">
                 <div class="col-11">
                     <input style="margin: unset;border: none !important;box-shadow: none !important;margin-bottom: -20px;padding: unset;font-weight:500"
                         type="{{ $type }}" class="form-control {{ $class }}" id="{{ $name }}Id"
                         aria-describedby="{{ $name }}Help" name="{{ $name }}"
                         placeholder="{{ $placeholder }}" @if ($is_required) required @endif>
                 </div>
             </div>
         </div>
     </div>
 </div>