document.addEventListener('DOMContentLoaded', () => {
    const cuotas = document.getElementById('imp-cuo').value;
    const total = document.getElementById('imp-amo').value;
    for (let i = 1; i <= cuotas; i++) {
        let inp = document.getElementById('imp-cuo-'+i);
        imp.addEventListener('change', checkTot(cuotas, total));
    }
});

const checkTot = (cuotas, total) => {
    let presTot = 0;
    for (let i = 1; i <= cuotas; i++) {
        presTot += document.getElementById('imp-cuo-'+i).value;
    }
    if (presTot != total) {
        document.getEelementById('sum-warn').style.display = 'block';
    }
    else {
        document.getEelementById('sum-warn').style.display = 'none';
    }
}
