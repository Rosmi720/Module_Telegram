<?php
$language = (!empty($language) && class_exists($language)) ? $language : \XcVm\Module\Telegram\TelegramTranslator::class;
?>
<script>
(function() {
    var txtSending = <?= json_encode($language::get('sending')); ?>;
    var txtStatusUpdated = <?= json_encode($language::get('status_updated')); ?>;
    var txtStatusUpdateFailed = <?= json_encode($language::get('status_update_failed')); ?>;
    var txtNetError = <?= json_encode($language::get('net_error')); ?>;
    var txtTestSent = <?= json_encode($language::get('test_broadcast_sent')); ?>;
    var txtTestFailed = <?= json_encode($language::get('test_broadcast_failed')); ?>;
    var txtDeleteModalTitle = <?= json_encode($language::get('delete_bot_modal_title')); ?>;
    var txtDeleteConfirm = <?= json_encode($language::get('delete_bot_confirm_text')); ?>;
    var txtConfirmBtn = <?= json_encode($language::get('confirm_delete_btn')); ?>;
    var txtCancelBtn = <?= json_encode($language::get('cancel')); ?>;
    var txtBotDeleted = <?= json_encode($language::get('bot_deleted')); ?>;
    var txtDeleteFailed = <?= json_encode($language::get('bot_delete_failed')); ?>;

    function toast(type, msg) {
        if (window.xcToast) {
            window.xcToast(msg, type);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type === 'error' ? 'error' : 'success',
                title: msg,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            alert(msg);
        }
    }

    // Search filter
    var searchInput = document.getElementById('botSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var val = this.value.trim().toLowerCase();
            document.querySelectorAll('.bot-card-wrapper').forEach(function(card) {
                var name = card.getAttribute('data-name') || '';
                var user = card.getAttribute('data-user') || '';
                var chat = card.getAttribute('data-chat') || '';
                if (name.includes(val) || user.includes(val) || chat.includes(val)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Toggle Active Status
    document.querySelectorAll('.js-toggle-status').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            var id = this.getAttribute('data-id');
            var isChecked = this.checked ? 1 : 0;
            var el = this;

            fetch('./api?action=telegram_bot_toggle&id=' + encodeURIComponent(id) + '&status=' + isChecked, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.result) {
                    toast('success', data.message || txtStatusUpdated);
                } else {
                    el.checked = !el.checked;
                    toast('error', data.message || txtStatusUpdateFailed);
                }
            })
            .catch(function() {
                el.checked = !el.checked;
                toast('error', txtNetError);
            });
        });
    });

    // Test Broadcast Action
    document.querySelectorAll('.js-btn-test').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> ' + txtSending;

            fetch('./api?action=telegram_bot_broadcast_test&bot_id=' + encodeURIComponent(id), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (data.result) {
                    toast('success', data.message || txtTestSent);
                } else {
                    toast('error', data.message || txtTestFailed);
                }
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                toast('error', txtNetError);
            });
        });
    });

    // Delete Bot Action
    document.querySelectorAll('.js-btn-delete').forEach(function(el) {
        el.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            var cardWrapper = this.closest('.bot-card-wrapper');

            var confirmPromise = typeof Swal !== 'undefined'
                ? Swal.fire({
                    title: txtDeleteModalTitle,
                    text: txtDeleteConfirm,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea5455',
                    cancelButtonColor: '#82868b',
                    confirmButtonText: txtConfirmBtn,
                    cancelButtonText: txtCancelBtn
                }).then(function(r) { return r.isConfirmed; })
                : Promise.resolve(window.confirm(name + ': ' + txtDeleteConfirm));

            confirmPromise.then(function(confirmed) {
                if (!confirmed) return;

                fetch('./api?action=telegram_bot_delete&id=' + encodeURIComponent(id), {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.result) {
                        toast('success', data.message || txtBotDeleted);
                        if (cardWrapper) {
                            cardWrapper.remove();
                        }
                    } else {
                        toast('error', data.message || txtDeleteFailed);
                    }
                })
                .catch(function() {
                    toast('error', txtNetError);
                });
            });
        });
    });
})();
</script>
</body>
</html>
