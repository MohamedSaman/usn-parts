<div class="container-fluid py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-gear-fill text-success fs-2"></i>
        <div class="ms-3">
            <h1 class="h3 fw-bold mb-0">System Settings</h1>
            <p class="text-muted mb-0">Manage all system configurations.</p>
        </div>
    </div>

    {{-- Accordion --}}
    <div class="accordion" id="settingsAccordion">
        <div class="accordion-item border-0 mb-4 shadow-sm rounded-4">
            <h2 class="accordion-header" id="headingSystemConfigs">
                <button class="accordion-button fw-semibold bg-white text-dark rounded-4"
                    type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseSystemConfigs" aria-expanded="true"
                    aria-controls="collapseSystemConfigs">
                    <i class="bi bi-sliders fs-5 me-3 text-success"></i>
                    System Configurations
                </button>
            </h2>
            <div id="collapseSystemConfigs" class="accordion-collapse collapse show"
                aria-labelledby="headingSystemConfigs" data-bs-parent="#settingsAccordion">
                <div class="accordion-body">

                    {{-- Add Button inside accordion --}}
                    <div class="mb-3 d-flex justify-content-end">
                        <button class="btn btn-primary shadow-sm" wire:click="openAddModal">
                            <i class="bi bi-plus-circle"></i> Add Configuration
                        </button>
                    </div>

                    {{-- Existing Configurations --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            @if($settings->isNotEmpty())
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-dark fw-bold">Key</th>
                                        <th class="text-dark fw-bold">Value</th>
                                        <th class="text-center text-dark fw-bold" style="width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $setting)
                                    <tr>
                                        <td class="text-dark">{{ $setting->key }}</td>
                                        <td class="text-dark">{{ $setting->value }}</td>
                                        <td class="text-center">
    <div class="dropdown">
        <button class="btn btn-sm btn-light border-0 dropdown-toggle" 
                type="button" 
                data-bs-toggle="dropdown" 
                aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li>
                <a class="dropdown-item text-primary" 
                   href="#" 
                   wire:click.prevent="openEditModal({{ $setting->id }})">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            </li>
            <li>
                <a class="dropdown-item text-danger" 
                   href="#" 
                   wire:click.prevent="confirmDelete({{ $setting->id }})">
                    <i class="bi bi-trash me-2"></i>Delete
                </a>
            </li>
        </ul>
    </div>
</td>


                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                No configurations found. <br>
                                <small>Click "Add Configuration" to create your first setting.</small>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Add/Edit --}}
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" wire:key="modal-{{ $isEdit ? 'edit' : 'add' }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">
                        @if($isEdit)
                        <i class="bi bi-pencil-square"></i> Edit Configuration
                        @else
                        <i class="bi bi-plus-circle"></i> Add Configuration
                        @endif
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <form wire:submit.prevent="{{ $isEdit ? 'updateConfiguration' : 'saveConfiguration' }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Key</label>
                            <input type="text" wire:model="key"
                                class="form-control @error('key') is-invalid @enderror"
                                placeholder="Enter configuration key">
                            @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Value</label>
                            <input type="text" wire:model="value"
                                class="form-control @error('value') is-invalid @enderror"
                                placeholder="Enter configuration value">
                            @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary shadow-sm" wire:click="closeModal" wire:loading.attr="disabled">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success shadow-sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="bi bi-check-circle"></i>
                                @if($isEdit)
                                Update Configuration
                                @else
                                Save Configuration
                                @endif
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>

@push('styles')
<style>
    .list-group-item {
        background-color: #fff;
        transition: all 0.2s ease-in-out;
        border: 1px solid #dee2e6;
    }

    .list-group-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    }

    .modal.fade.show {
        display: block !important;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table-bordered {
        border-color: #dee2e6;
    }

    .accordion-button:not(.collapsed) {
        background-color: #fff;
        color: #000;
        box-shadow: none;
    }

    .accordion-button:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .accordion-body {
        padding: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // SweetAlert for delete confirmation
    window.addEventListener('swal:confirm-delete', event => {
        Swal.fire({
            title: 'Are you sure?',
            text: "This configuration will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('deleteConfirmed', {
                    id: event.detail.id
                });
            }
        });
    });
</script>
@endpush