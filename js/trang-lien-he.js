const form = document.querySelector('#form-lien-he');
const nutGui = document.querySelector('#nut-gui-lien-he');
const theKetQua = document.querySelector('#ket-qua-form');

const cacTruongCanKiemTra = [
    { id: '#fullname', idLoi: '#loi-fullname', msgRong: 'Vui lòng nhập họ và tên.' },
    { id: '#email', idLoi: '#loi-email', msgRong: 'Vui lòng nhập email.' },
    { id: '#message', idLoi: '#loi-message', msgRong: 'Vui lòng nhập nội dung tin nhắn.' }
];

function kiemTraInput(inputEl, loiEl, thongBaoRong) {
    if (inputEl.validity.valueMissing) {
        loiEl.textContent = thongBaoRong;
        inputEl.setCustomValidity(thongBaoRong);
        return false;
    } else if (inputEl.validity.typeMismatch || inputEl.validity.patternMismatch) {
        loiEl.textContent = 'Dữ liệu nhập vào chưa đúng định dạng.';
        inputEl.setCustomValidity('Sai định dạng');
        return false;
    } else if (inputEl.validity.tooShort) {
        loiEl.textContent = `Vui lòng nhập tối thiểu ${inputEl.getAttribute('minlength')} ký tự.`;
        inputEl.setCustomValidity('Quá ngắn');
        return false;
    }
    
    loiEl.textContent = '';
    inputEl.setCustomValidity('');
    return true;
}

cacTruongCanKiemTra.forEach(item => {
    const input = document.querySelector(item.id);
    const theLoi = document.querySelector(item.idLoi);
    if(input) {
         input.addEventListener('blur', () => {
             kiemTraInput(input, theLoi, item.msgRong);
         });
    }
});

form.addEventListener('submit', async (suKien) => {
    suKien.preventDefault();

    let formHopLe = true;
    const duLieuThuThap = {};

    cacTruongCanKiemTra.forEach(item => {
        const input = document.querySelector(item.id);
        const theLoi = document.querySelector(item.idLoi);
        if(input) {
            const hopLe = kiemTraInput(input, theLoi, item.msgRong);
            if (!hopLe) formHopLe = false;
            duLieuThuThap[input.name] = input.value;
        }
    });

    if (!formHopLe) {
        theKetQua.textContent = 'Vui lòng kiểm tra lại thông tin bị lỗi.';
        theKetQua.style.color = '#dc2626'; 
        return;
    }

    nutGui.disabled = true;
    theKetQua.textContent = 'Đang gửi biểu mẫu...';
    theKetQua.style.color = '#2563eb'; 

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(duLieuThuThap)
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        theKetQua.textContent = 'Gửi liên hệ thành công!';
        theKetQua.style.color = 'green';
        form.reset(); 

    } catch (loi) {
        theKetQua.textContent = 'Không tải được dữ liệu, vui lòng thử lại.';
        theKetQua.style.color = '#dc2626';
        console.error(loi);
    } finally {
        nutGui.disabled = false;
    }
});