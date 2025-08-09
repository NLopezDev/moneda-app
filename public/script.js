const CURRENCIES = ["USD","EUR","ARS","BRL","CLP","UYU","MXN","COP","GBP","JPY","CHF","CAD","AUD","CNY","HKD","INR","RUB","ZAR","KRW","SGD","NZD"];

const $from   = document.getElementById("cur-from");
const $to     = document.getElementById("cur-to");
const $amount = document.getElementById("amount");
const $form   = document.getElementById("convert-form");
const $result = document.getElementById("result");
const $swap   = document.getElementById("btn-swap");
const $btn    = document.getElementById("btn-convert");

function ensureOptions(){
  if ($from.options.length <= 3 || $to.options.length <= 3){
    const fill = (sel, preferred) => {
      const seen = new Set([...sel.options].map(o=>o.value));
      for (const code of CURRENCIES){
        if (seen.has(code)) continue;
        const opt = document.createElement("option");
        opt.value = code; opt.textContent = code;
        if (code === preferred) opt.selected = true;
        sel.appendChild(opt);
      }
    };
    fill($from, "USD");
    fill($to, "ARS");
  }
}
const nf = new Intl.NumberFormat("es-AR",{maximumFractionDigits:4});
const fmt = (n)=> nf.format(n);

async function fetchRate(amount, from, to){
  const qs = new URLSearchParams({amount, from, to}).toString();
  const r = await fetch(`/moneda-app/api/convert.php?${qs}`, { headers:{ "Accept":"application/json" } });
  if (!r.ok) throw new Error(`HTTP ${r.status}`);
  return r.json();
}

function swapCurrencies(){ const a=$from.value; $from.value=$to.value; $to.value=a; }

function handleConvert(e){
  if (e) e.preventDefault(); // <- clave para evitar el refresh
  const amount = parseFloat($amount.value);
  const from = $from.value, to = $to.value;

  if (!Number.isFinite(amount) || amount < 0){
    $result.innerHTML = `<span class="error">El monto debe ser válido (≥ 0).</span>`;
    return;
  }
  if (from === to){
    $result.textContent = `${fmt(amount)} ${from} = ${fmt(amount)} ${to}`;
    return;
  }

  $result.textContent = "Convirtiendo…";
  fetchRate(amount, from, to)
    .then(({ converted, rate, provider, ts })=>{
      const when = new Date(ts).toLocaleString("es-AR");
      $result.innerHTML = `
        <div><strong>${fmt(amount)} ${from}</strong> = <strong>${fmt(converted)} ${to}</strong></div>
        <div class="muted">Tasa: 1 ${from} = ${fmt(rate)} ${to} • Origen: ${provider} • ${when}</div>
      `;
    })
    .catch(err=>{
      console.error(err);
      $result.innerHTML = `<span class="error">No pude convertir ahora. Probá de nuevo.</span>`;
    });
}

document.addEventListener("DOMContentLoaded", ()=>{
  console.log("JS cargado ✅");
  ensureOptions();
  if ($swap) $swap.addEventListener("click", swapCurrencies);
  if ($form) $form.addEventListener("submit", handleConvert);
  if ($btn)  $btn.addEventListener("click", handleConvert); // respaldo extra
  
  // Mejoras para dispositivos móviles
  if ('serviceWorker' in navigator) {
    // Registrar service worker para PWA (opcional)
    // navigator.serviceWorker.register('/sw.js');
  }
  
  // Prevenir zoom en inputs en iOS
  const inputs = document.querySelectorAll('input[type="number"]');
  inputs.forEach(input => {
    input.addEventListener('focus', () => {
      input.style.fontSize = '16px';
    });
  });
  
  // Mejorar experiencia táctil
  if ('ontouchstart' in window) {
    document.body.classList.add('touch-device');
  }
});