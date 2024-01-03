document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('sum-warn').style.display = 'none';
    const cuotas = document.getElementById('imp-cuo').value;
    const total = document.getElementById('imp-amo').value;
    for (let i = 1; i <= cuotas; i++) {
        let inp = document.getElementById('imp-cuo-'+i);
        inp.addEventListener('keyup', () => {checkTot(cuotas, total)});
    }
});

const checkTot = (cuotas, total) => {
    let presTot = 0;
    for (let i = 1; i <= cuotas; i++) {
        presTot += +document.getElementById('imp-cuo-'+i).value;
    }
    console.log(total);
    console.log(presTot);
    if (presTot != total) {
        document.getElementById('sum-warn').style.display = 'block';
        document.getElementById('imp-sub').disabled = true;
    }
    else {
        document.getElementById('sum-warn').style.display = 'none';
        document.getElementById('imp-sub').disabled = false;
    }
}
