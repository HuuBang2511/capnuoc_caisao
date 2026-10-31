<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Cấu hình đồng hồ khách hàng';
?>
<style>
.cfg-wrap { max-width:960px; margin:0 auto; padding:16px; }
.cfg-head { display:flex; align-items:center; justify-content:space-between;
            margin-bottom:16px; flex-wrap:wrap; gap:10px; }
.cfg-head h2 { font-size:1.05rem; font-weight:700; color:#1e3a5f; margin:0; }
.btn-add { padding:9px 20px; background:#3b82f6; color:#fff; border:none;
           border-radius:8px; font-size:.88rem; font-weight:600; cursor:pointer; }
.btn-back { padding:9px 16px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;
            border-radius:8px; font-size:.88rem; cursor:pointer; text-decoration:none; }
/* Bảng danh sách */
.cfg-table { width:100%; border-collapse:collapse; font-size:.82rem;
             background:#fff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; }
.cfg-table thead th { background:#1e3a5f; color:#fff; padding:10px 12px;
                      text-align:left; white-space:nowrap; }
.cfg-table tbody tr:hover { background:#f0f4f8; }
.cfg-table td { padding:10px 12px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
.cfg-table td.channel { font-family:monospace; font-size:.75rem; color:#1d4ed8;
                         background:#eff6ff; border-radius:4px; padding:3px 7px; }
.cfg-table td.channel-out { font-family:monospace; font-size:.75rem; color:#dc2626;
                              background:#fff1f2; border-radius:4px; padding:3px 7px; }
.badge-active   { background:#dcfce7; color:#166534; padding:2px 8px;
                  border-radius:99px; font-size:.72rem; font-weight:600; }
.badge-inactive { background:#fee2e2; color:#991b1b; padding:2px 8px;
                  border-radius:99px; font-size:.72rem; font-weight:600; }
.btn-edit { padding:4px 12px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;
            border-radius:6px; font-size:.75rem; cursor:pointer; }
.btn-del  { padding:4px 12px; background:#fff1f2; color:#dc2626; border:1px solid #fecaca;
            border-radius:6px; font-size:.75rem; cursor:pointer; }
/* Modal */
.modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; }
.modal-backdrop.show { display:flex; align-items:center; justify-content:center; padding:16px; }
.modal-box { background:#fff; border-radius:14px; padding:24px; width:100%; max-width:520px;
             box-shadow:0 20px 60px rgba(0,0,0,.25); max-height:90vh; overflow-y:auto; }
.modal-title { font-size:1rem; font-weight:700; color:#1e3a5f; margin-bottom:18px; }
.form-field { margin-bottom:14px; }
.form-field label { font-size:.78rem; color:#64748b; display:block; margin-bottom:4px; font-weight:600; }
.form-field input, .form-field select, .form-field textarea {
    width:100%; padding:9px 11px; border:1.5px solid #e2e8f0; border-radius:8px;
    font-size:.88rem; outline:none; background:#fff; }
.form-field input:focus, .form-field select:focus, .form-field textarea:focus { border-color:#3b82f6; }
.form-field .hint { font-size:.7rem; color:#94a3b8; margin-top:3px; }
.modal-footer { display:flex; gap:10px; margin-top:18px; }
.btn-save   { flex:1; padding:11px; background:#3b82f6; color:#fff; border:none;
              border-radius:8px; font-weight:600; cursor:pointer; font-size:.9rem; }
.btn-cancel { padding:11px 20px; background:#f1f5f9; color:#475569; border:none;
              border-radius:8px; cursor:pointer; font-size:.9rem; }
</style>

<div class="cfg-wrap">
    <div class="cfg-head">
        <h2>⚙️ Cấu hình đồng hồ — Khách hàng</h2>
        <div style="display:flex;gap:8px;">
            <a href="<?= Url::to(['nhat-ky/san-luong-dong-ho']) ?>" class="btn-back">← Về báo cáo</a>
            <button class="btn-add" onclick="openModal()">＋ Thêm khách hàng</button>
        </div>
    </div>

    <div style="font-size:.78rem;color:#64748b;background:#fffbeb;border:1px solid #fcd34d;
                border-radius:8px;padding:10px 14px;margin-bottom:16px;">
        💡 <strong>Hướng dẫn:</strong>
        Mỗi dòng là 1 khách hàng. "Đồng hồ vào" là Channel ID đầu vào, "Đồng hồ ra" là các Channel ID cần trừ đi.
        Ví dụ KCN NT6: vào = <code>60007</code>, ra = <code>60019,60021,60022,60016</code> → Sản lượng = 60007 − (60019+60021+60022+60016).
        Nếu không cần trừ, để trống "Đồng hồ ra".
    </div>

    <div style="overflow-x:auto;">
    <table class="cfg-table" id="cfg-tbl">
        <thead>
            <tr>
                <th style="width:40px">#</th>
                <th>Tên khách hàng</th>
                <th>Đ.hồ đầu vào</th>
                <th>Đ.hồ đầu ra (trừ)</th>
                <th style="width:60px">Trạng thái</th>
                <th style="width:100px">Thao tác</th>
            </tr>
        </thead>
        <tbody id="cfg-body">
        <?php foreach ($danhSach as $i => $kh): ?>
            <tr id="row-<?= $kh->id ?>">
                <td style="color:#94a3b8"><?= $kh->thu_tu ?: ($i+1) ?></td>
                <td>
                    <strong><?= Html::encode($kh->ten_kh) ?></strong>
                    <?php if ($kh->ghi_chu): ?>
                    <div style="font-size:.7rem;color:#94a3b8;margin-top:2px"><?= Html::encode($kh->ghi_chu) ?></div>
                    <?php endif; ?>
                </td>
                <td><span class="channel"><?= Html::encode($kh->getLabelDauVao()) ?></span></td>
                <td>
                    <?php $ra = $kh->getLabelDauRa(); ?>
                    <?= $ra !== '—'
                        ? '<span class="channel-out">'.Html::encode($ra).'</span>'
                        : '<span style="color:#cbd5e1">—</span>' ?>
                </td>
                <td>
                    <span class="<?= $kh->active ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $kh->active ? 'Bật' : 'Tắt' ?>
                    </span>
                </td>
                <td style="white-space:nowrap;">
                    <button class="btn-edit" onclick="editRow(<?= htmlspecialchars(json_encode([
                        'id'              => $kh->id,
                        'ten_kh'          => $kh->ten_kh,
                        'thu_tu'          => $kh->thu_tu,
                        'channel_dau_vao' => $kh->getLabelDauVao(),
                        'channel_dau_ra'  => $kh->getLabelDauRa() === '—' ? '' : $kh->getLabelDauRa(),
                        'don_vi'          => $kh->don_vi,
                        'ghi_chu'         => $kh->ghi_chu,
                        'active'          => $kh->active,
                    ]), ENT_QUOTES) ?>)">✏️ Sửa</button>
                    <button class="btn-del" onclick="delRow(<?= $kh->id ?>, '<?= Html::encode($kh->ten_kh) ?>')">🗑</button>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($danhSach)): ?>
            <tr><td colspan="6" style="text-align:center;padding:30px;color:#94a3b8;">
                Chưa có cấu hình nào. Nhấn "+ Thêm khách hàng" để bắt đầu.
            </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Modal thêm/sửa -->
<div class="modal-backdrop" id="modal">
    <div class="modal-box">
        <div class="modal-title" id="modal-title">Thêm khách hàng</div>
        <input type="hidden" id="edit-id" value="">

        <div class="form-field">
            <label>Tên khách hàng <span style="color:#ef4444">*</span></label>
            <input type="text" id="f-ten_kh" placeholder="VD: KCN NT6, HTX Hưng Lộc...">
        </div>
        <div class="form-field">
            <label>Thứ tự hiển thị</label>
            <input type="number" id="f-thu_tu" value="0" min="0" step="1">
        </div>
        <div class="form-field">
            <label>Channel ID đầu vào <span style="color:#ef4444">*</span></label>
            <input type="text" id="f-channel_dau_vao" placeholder="VD: 60007   hoặc   60015,60017">
            <div class="hint">Nhập 1 hoặc nhiều Channel ID, cách nhau bằng dấu phẩy. Sản lượng = tổng các channel này.</div>
        </div>
        <div class="form-field">
            <label>Channel ID đầu ra (trừ đi) — để trống nếu không cần</label>
            <input type="text" id="f-channel_dau_ra" placeholder="VD: 60019,60021,60022,60016">
            <div class="hint">Sản lượng thực = Đầu vào − Đầu ra. Dùng cho NT6, NT5...</div>
        </div>
        <div class="form-field">
            <label>Đơn vị</label>
            <select id="f-don_vi">
                <option value="m³">m³</option>
            </select>
        </div>
        <div class="form-field">
            <label>Ghi chú</label>
            <input type="text" id="f-ghi_chu" placeholder="Ghi chú thêm nếu có...">
        </div>
        <div class="form-field">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" id="f-active" checked style="width:auto;"> Đang sử dụng (hiển thị trong báo cáo)
            </label>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Hủy</button>
            <button class="btn-save" onclick="saveRow()">💾 Lưu</button>
        </div>
    </div>
</div>

<script>
const SAVE_URL = '<?= Url::to(['nhat-ky/dong-ho-save']) ?>';
const DEL_URL  = '<?= Url::to(['nhat-ky/dong-ho-delete']) ?>';

function openModal(data) {
    document.getElementById('modal-title').textContent = data ? 'Sửa khách hàng' : 'Thêm khách hàng';
    document.getElementById('edit-id').value         = data?.id ?? '';
    document.getElementById('f-ten_kh').value        = data?.ten_kh ?? '';
    document.getElementById('f-thu_tu').value        = data?.thu_tu ?? 0;
    document.getElementById('f-channel_dau_vao').value = data?.channel_dau_vao ?? '';
    document.getElementById('f-channel_dau_ra').value  = data?.channel_dau_ra  ?? '';
    document.getElementById('f-don_vi').value        = data?.don_vi ?? 'm³';
    document.getElementById('f-ghi_chu').value       = data?.ghi_chu ?? '';
    document.getElementById('f-active').checked      = data?.active !== false;
    document.getElementById('modal').classList.add('show');
    document.getElementById('f-ten_kh').focus();
}
function editRow(data) { openModal(data); }
function closeModal() { document.getElementById('modal').classList.remove('show'); }

function saveRow() {
    const ten = document.getElementById('f-ten_kh').value.trim();
    const vao = document.getElementById('f-channel_dau_vao').value.trim();
    if (!ten) { alert('Vui lòng nhập tên khách hàng'); return; }
    if (!vao) { alert('Vui lòng nhập ít nhất 1 Channel ID đầu vào'); return; }

    const payload = new FormData();
    const id = document.getElementById('edit-id').value;
    if (id) payload.append('id', id);
    payload.append('ten_kh',          ten);
    payload.append('thu_tu',          document.getElementById('f-thu_tu').value);
    payload.append('channel_dau_vao', vao);
    payload.append('channel_dau_ra',  document.getElementById('f-channel_dau_ra').value.trim());
    payload.append('don_vi',          document.getElementById('f-don_vi').value);
    payload.append('ghi_chu',         document.getElementById('f-ghi_chu').value.trim());
    payload.append('active',          document.getElementById('f-active').checked ? 1 : 0);
    payload.append(yii.getCsrfParam(), yii.getCsrfToken());

    fetch(SAVE_URL, { method:'POST', body:payload })
        .then(r => r.json())
        .then(d => {
            if (d.success) { closeModal(); location.reload(); }
            else alert('Lỗi: ' + d.msg);
        });
}

function delRow(id, name) {
    if (!confirm(`Xóa khách hàng "${name}"?`)) return;
    const fd = new FormData();
    fd.append(yii.getCsrfParam(), yii.getCsrfToken());
    fetch(DEL_URL + '?id=' + id, { method:'POST', body:fd })
        .then(r => r.json())
        .then(d => { if (d.success) document.getElementById('row-'+id)?.remove(); });
}

// Đóng modal khi click nền
document.getElementById('modal').addEventListener('click', e => {
    if (e.target === document.getElementById('modal')) closeModal();
});
</script>
