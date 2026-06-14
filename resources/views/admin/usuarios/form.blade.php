<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="userForm">
                @csrf
                <input type="hidden" id="user_id" name="user_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel"><i class="fas fa-user me-1"></i>Novo Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Nome completo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="email@exemplo.com" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha <span class="text-danger" id="passwordRequired">*</span></label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" minlength="8" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Senha <span class="text-danger" id="passwordConfirmRequired">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repita a senha" minlength="8" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profile_id" class="form-label">Perfil de Acesso <span class="text-danger">*</span></label>
                                <select id="profile_id" name="profile_id" class="form-select" required>
                                    <option value="">Selecione um perfil</option>
                                    @foreach($profiles ?? [] as $profile)
                                        <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Foto/Avatar</label>
                                <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" id="active" name="active" class="form-check-input" value="1" checked>
                                    <label for="active" class="form-check-label">Usuário Ativo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveUser"><i class="fas fa-save me-1"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#userModal').on('hidden.bs.modal', function() {
            $('#userForm')[0].reset();
            $('#user_id').val('');
            $('#userModalLabel').text('Novo Usuário');
            $('#password').prop('required', true);
            $('#password_confirmation').prop('required', true);
            $('#passwordRequired').show();
            $('#passwordConfirmRequired').show();
        });

        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btnSaveUser');
            var id = $('#user_id').val();
            var url = id ? '{{ route("admin.users.update", ":id") }}'.replace(':id', id) : '{{ route("admin.users.store") }}';
            var method = id ? 'PUT' : 'POST';
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Salvando...');

            var formData = new FormData(this);
            if (id) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (window.isSuccessfulResponse(res)) {
                        toastr.success(res.message || 'Usuário salvo com sucesso!');
                        $('#userModal').modal('hide');
                        if (typeof table !== 'undefined') table.ajax.reload();
                    } else {
                        toastr.error(res.message || 'Erro ao salvar usuário.');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(field, msgs) {
                            $.each(msgs, function(i, msg) { toastr.error(msg); });
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Erro ao salvar usuário.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });
    });
</script>
