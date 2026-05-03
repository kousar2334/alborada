<!-- Media Modal -->
<div class="modal fade" id="mediaManagerModal" tabindex="-1" role="dialog" aria-labelledby="mediaManagerModal"
    aria-hidden="true">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header bg-blue py-2">
                <h5 class="modal-title">
                    <i class="fas fa-photo-video"></i> {{ __tr('Media Library') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-0 pb-2 pt-0">
                @include('backend.media.media_list')
            </div>
        </div>
    </div>
</div>
<!-- End Media Modal -->
