 <form id="member-update-form">
     <div class="form-row mb-2">
         <label class="black font-14 col-12">{{ __tr('Image') }}</label>
         <x-media name="edit_image" :value="$member->image"></x-media>
     </div>
     <div class="form-row mb-2">
         <label class="black font-14">{{ __tr('Name') }}</label>
         <input type="hidden" name="id" value="{{ $member->id }}">
         <input type="text" name="name" value="{{ $member->name }}" class="form-control">
     </div>
     <div class="form-row mb-2">
         <label class="black font-14">{{ __tr('Email') }}</label>
         <input type="email" name="email" value="{{ $member->email }}" class="form-control">
     </div>
     <div class="form-row mb-2">
         <label class="col-12 black font-14">{{ __tr('Phone') }}</label>
         <input type="text" name="phone" value="{{ $member->phone }}" class="form-control">
     </div>
     <div class="form-row mb-2">
         <label class="col-12 black font-14">{{ __tr('Status') }}</label>
         <select name="status" class="form-control">
             <option value="{{ config('settings.general_status.active') }}" @selected($member->status == config('settings.general_status.active'))>
                 {{ __tr('Active') }}
             </option>
             <option value="{{ config('settings.general_status.in_active') }}" @selected($member->status == config('settings.general_status.in_active'))>
                 {{ __tr('Inactive') }}
             </option>
         </select>
     </div>
     <div class="btn-area d-flex justify-content-between">
         <button class="btn btn-primary mt-2 update-member">{{ __tr('Save Changes') }}</button>
     </div>

 </form>
