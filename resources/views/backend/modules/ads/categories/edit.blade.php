 <form id="editForm">
     @csrf
     <input type="hidden" name="id" value="{{ $category->id }}">
     <div class="form-row mb-20">
         <div class="form-group col-lg-6">
             <label class="black font-14">{{ __tr('Icon') }}</label>
             <x-media name="icon_edit" :value="$category->icon"></x-media>
         </div>
         <div class="form-group col-lg-6">
             <label class="black font-14">{{ __tr('Featured Image') }}</label>
             <x-media name="image_edit" :value="$category->image"></x-media>
         </div>
     </div>

     <div class="form-row">
         <div class="form-group col-lg-12">
             <label class="black font-14">{{ __tr('Title') }}</label>
             <input type="text" name="title" class="form-control slugable_input"
                 placeholder="{{ __tr('Enter title') }}" value="{{ $category->title }}">
         </div>
     </div>

     <div class="form-row">
         <div class="form-group col-lg-12">
             <label class="font-14 bold black w-100">{{ __tr('Parent') }} </label>
             <select class="parent-options form-control w-100" name="parent">
                 @if ($category->parentCategory != null)
                     <option value="{{ $category->parentCategory->id }}" selected>
                         {{ $category->parentCategory->title }}
                     </option>
                 @endif
             </select>
             @if ($errors->has('parent'))
                 <div class="invalid-input">{{ $errors->first('parent') }}</div>
             @endif
         </div>
     </div>

     <div class="form-row">
         <div class="form-group col-lg-12">
             <label class="black font-14">{{ __tr('Status') }}</label>
             <select name="status" class="form-control" required>
                 <option value="{{ config('settings.general_status.active') }}" @selected($category->status == config('settings.general_status.active'))>
                     {{ __tr('Active') }}
                 </option>
                 <option value="{{ config('settings.general_status.in_active') }}" @selected($category->status == config('settings.general_status.in_active'))>
                     {{ __tr('Inactive') }}
                 </option>
             </select>
         </div>
     </div>
     <div class="btn-area d-flex justify-content-between">
         <button class="btn btn-primary mt-2 store-category">{{ __tr('Save Changes') }}</button>
     </div>

 </form>
