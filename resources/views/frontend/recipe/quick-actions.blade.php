<style>
    .modal-title{color: #e2ae76}
</style>

{{-- resources/views/frontend/recipe/quick-actions.blade.php --}}
<section id="qa-banner" class="mini-banner reveal" aria-label="Tutorials e risorse">
    <div class="mini-banner-head d-flex align-items-center mb-2">
        <span class="banner-kicker">Nuovo</span>
        <h6 class="banner-title ms-2">Consigli rapidi &amp; Tutorial</h6>
        <button type="button" class="ms-auto banner-cta" data-bs-toggle="modal" data-bs-target="#tipAll">
            Vedi tutti <i class="bi bi-arrow-right-short"></i>
        </button>
    </div>

    {{-- <button class="scroll-arrow left" type="button" aria-label="Scorri a sinistra"><i class="bi bi-chevron-left"></i></button>
    <button class="scroll-arrow right" type="button" aria-label="Scorri a destra"><i class="bi bi-chevron-right"></i></button> --}}

    <div class="scroller" tabindex="0">
        <button type="button" class="chip chip--cta" data-bs-toggle="modal" data-bs-target="#tipVideo">
            <span class="icon-badge"><i class="bi bi-play-circle"></i></span>Video: ricetta perfetta<span class="shine"></span>
        </button>

        <button type="button" class="chip" data-bs-toggle="modal" data-bs-target="#tipCostiKg">
            <span class="icon-badge"><i class="bi bi-currency-euro"></i></span>Costi al kg (guida)<span class="shine"></span>
        </button>

        <button type="button" class="chip" data-bs-toggle="modal" data-bs-target="#tipPrezzi">
            <span class="icon-badge"><i class="bi bi-bag-check"></i></span>Prezzi di vendita: best practice<span class="shine"></span>
        </button>

        <button type="button" class="chip chip--hot" data-bs-toggle="modal" data-bs-target="#tipMarginiIva">
            <span class="icon-badge"><i class="bi bi-graph-up-arrow"></i></span>Margini &amp; IVA spiegati<span class="ping"></span><span class="shine"></span>
        </button>

        <button type="button" class="chip" data-bs-toggle="modal" data-bs-target="#tipManodopera">
            <span class="icon-badge"><i class="bi bi-speedometer2"></i></span>Ottimizza manodopera<span class="shine"></span>
        </button>

        <button type="button" class="chip" data-bs-toggle="modal" data-bs-target="#tipTemplateIng">
            <span class="icon-badge"><i class="bi bi-journal-text"></i></span>Template ingredienti<span class="shine"></span>
        </button>
    </div>
</section>

{{-- ====== Modal styles (scoped) ====== --}}
<style>
    .modal-glass{
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(226,174,118,.35);
    }
    .modal-glass .modal-header{
        background: linear-gradient(135deg,#0b2b53, #041930);
        color:#ffe5c7;
        border:0;
    }
    .modal-glass .btn-close{
        filter: invert(1) grayscale(1) brightness(200%);
        opacity:.7
    }
    .formula{
        background:#fff7ef;
        border:1px dashed rgba(226,174,118,.6);
        border-radius:12px;
        padding:12px 14px;
        font-weight:600;
    }
    .callout{
        background:#f8fafc;
        border-left:4px solid #e2ae76;
        border-radius:10px;
        padding:10px 12px;
    }
</style>

{{-- ====== Modals ====== --}}

{{-- See all --}}
<div class="modal fade" id="tipAll" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Consigli rapidi &amp; Tutorial — panoramica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Benvenuto! Qui trovi le basi operative per calcolare costi, prezzi e margini in modo semplice e coerente con il flusso dell’app.</p>
        <ul class="mb-0">
            <li><strong>1. Ingredienti →</strong> inserisci quantità in grammi e ottieni <em>costo materie</em>.</li>
            <li><strong>2. Manodopera →</strong> minuti × tariffa reparto = <em>costo lavoro</em>.</li>
            <li><strong>3. Imballaggio →</strong> aggiungi il costo unitario (per kg o per pezzo).</li>
            <li><strong>4. Costo unitario →</strong> €/kg o €/pz prima/dopo imballaggio.</li>
            <li><strong>5. Prezzo di vendita →</strong> scegli modalità (kg/pezzo), applica IVA, controlla <em>margine</em>.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Capito!</button>
      </div>
    </div>
  </div>
</div>

{{-- 1) Video: ricetta perfetta --}}
<div class="modal fade" id="tipVideo" tabindex="-1" aria-hidden="true" aria-labelledby="tipVideoLabel">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="tipVideoLabel" class="modal-title fw-bold">Video: ricetta perfetta — flusso in 3 minuti</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <div class="ratio ratio-16x9 rounded mb-3" style="border:1px solid rgba(226,174,118,.35);">
          <iframe src="https://www.youtube.com/embed/HhC75Ion8fA?rel=0&modestbranding=1"
                  title="Guida rapida Pasticcere Pro" allowfullscreen></iframe>
        </div>
        <div class="callout mb-2"><strong>Checklist veloce:</strong> ingredienti → manodopera → imballaggio → prezzo → margine.</div>
        <ul class="mb-0">
          <li>Inserisci ingredienti in <strong>grammi</strong> (l’app calcola € con il listino €/kg).</li>
          <li>Seleziona <strong>reparto</strong> per usare la tariffa corretta €/min.</li>
          <li>Imposta imballaggio per dose (se a pezzo) o kg</li>
          <li>Controlla il <strong>margine</strong> (netto, prima dell’IVA) e regola il prezzo se serve.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Tutto chiaro</button>
      </div>
    </div>
  </div>
</div>

{{-- 2) Costi al kg (guida) --}}
<div class="modal fade" id="tipCostiKg" tabindex="-1" aria-hidden="true" aria-labelledby="tipCostiKgLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="tipCostiKgLabel" class="modal-title fw-bold">Costi al kg — guida pratica</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Il costo €/kg mette insieme materie prime e manodopera, rapportandoli al peso finito.</p>
        <div class="formula mb-3">
          Costo materie (€) + Costo manodopera (€) = <u>Totale produzione (€)</u><br>
          Peso finito (kg) = Peso dopo calo (g) / 1000<br>
          <strong>Costo €/kg (prima imballaggio) = Totale produzione / Peso finito</strong><br>
          <em>Costo €/kg (dopo imballaggio) = Costo €/kg + Imballaggio (€/kg)</em>
        </div>
        <p class="mb-2"><strong>Note:</strong></p>
        <ul class="mb-0">
          <li>Se vendi a pezzo, l’app converte in <em>€/pezzo</em> mantenendo la logica sopra.</li>
          <li>Controlla sempre il peso finito: incide direttamente sul costo unitario.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Ok, grazie</button>
      </div>
    </div>
  </div>
</div>

{{-- 3) Prezzi di vendita: best practice --}}
<div class="modal fade" id="tipPrezzi" tabindex="-1" aria-hidden="true" aria-labelledby="tipPrezziLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="tipPrezziLabel" class="modal-title fw-bold">Prezzi di vendita — best practice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Definisci prima il <strong>prezzo netto</strong> (senza IVA) e poi aggiungi l’IVA.</p>
        <div class="formula mb-3">
          <strong>Prezzo netto target = Costo unitario / (1 − Margine%)</strong><br>
          Prezzo lordo = Prezzo netto × (1 + IVA%)
        </div>
        <ul class="mb-2">
          <li><strong>Margine%</strong> consigliato per pasticceria da banco: 45–60% (varia per categoria).</li>
          <li><strong>Ricarico</strong> (markup) ≠ Margine%: Ricarico% = Margine / Costo.</li>
          <li>Usa “Suggerisci prezzo (×2.2)” come base, poi affina per mercato e qualità.</li>
        </ul>
        <div class="callout mb-0">Ricorda: il margine mostrato dall’app è <em>netto</em> (prima dell’IVA), così confronti mele con mele.</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Perfetto</button>
      </div>
    </div>
  </div>
</div>

{{-- 4) Margini & IVA spiegati --}}
<div class="modal fade" id="tipMarginiIva" tabindex="-1" aria-hidden="true" aria-labelledby="tipMarginiIvaLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="tipMarginiIvaLabel" class="modal-title fw-bold">Margini &amp; IVA — differenze e formule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <div class="formula mb-3">
          Margine (netto) = Prezzo netto − Costo unitario<br>
          <strong>Margine% = Margine / Prezzo netto</strong><br>
          Ricarico% = Margine / Costo unitario<br>
          Prezzo lordo = Prezzo netto × (1 + IVA%)
        </div>
        <ul class="mb-0">
          <li><strong>Perché netto?</strong> L’IVA è un’imposta a valle: non aumenta il guadagno.</li>
          <li><strong>Controllo rapido:</strong> se Margine% scende sotto il target, rivedi prezzo o costi.</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Chiaro</button>
      </div>
    </div>
  </div>
</div>

{{-- 5) Ottimizza manodopera --}}
<div class="modal fade" id="tipManodopera" tabindex="-1" aria-hidden="true" aria-labelledby="tipManodoperaLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="tipManodoperaLabel" class="modal-title fw-bold">Ottimizza manodopera — impatto reale sul costo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <div class="formula mb-3">
          <strong>Costo lavoro = Minuti × Tariffa (€/min) del reparto</strong>
        </div>
        <ul class="mb-2">
          <li>Usa il <strong>reparto</strong> corretto per caricare la tariffa giusta.</li>
          <li>Batching: aumentare la dose spesso <em>diluisce</em> il costo/min su ogni pezzo.</li>
          <li>Automazioni e mise en place riducono minuti “non produttivi”.</li>
        </ul>
        <div class="callout mb-0">Suggerimento: prova a raddoppiare la dose e verifica come cambia il costo unitario.</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Ottimo</button>
      </div>
    </div>
  </div>
</div>

{{-- 6) Template ingredienti --}}
<div class="modal fade" id="tipTemplateIng" tabindex="-1" aria-hidden="true" aria-labelledby="tipTemplateIngLabel">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-glass rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 id="tipTemplateIngLabel" class="modal-title fw-bold">Template ingredienti — ordine &amp; precisione</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <ul class="mb-3">
          <li>Usa nomi chiari e <strong>unità in grammi</strong> per coerenza nei calcoli.</li>
          <li>Aggiungi ricette-base come <em>ingredienti</em> (es. bagna, crema) per riuso e controllo costi.</li>
          <li>Aggiorna i <strong>prezzi €/kg</strong> dal listino fornitore quando variano.</li>
        </ul>
        <div class="formula mb-0">
          <strong>Costo riga ingrediente = (€/kg ÷ 1000) × grammi</strong>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-accent" data-bs-dismiss="modal">Perfetto</button>
      </div>
    </div>
  </div>
</div>

{{-- Tiny script: arrows for this banner only --}}
<script>
(function(){
    const wrap = document.getElementById('qa-banner');
    if (!wrap) return;
    const scroller = wrap.querySelector('.scroller');
    const left = wrap.querySelector('.scroll-arrow.left');
    const right = wrap.querySelector('.scroll-arrow.right');

    function step(){ return Math.max(200, Math.round(scroller.clientWidth * 0.6)); }
    function update(){
        const max = scroller.scrollWidth - scroller.clientWidth - 2;
        left.disabled  = scroller.scrollLeft <= 2;
        right.disabled = scroller.scrollLeft >= max;
        left.style.opacity  = left.disabled  ? .5 : 1;
        right.style.opacity = right.disabled ? .5 : 1;
    }
    left?.addEventListener('click', ()=> scroller.scrollBy({left: -step(), behavior:'smooth'}));
    right?.addEventListener('click',()=> scroller.scrollBy({left:  step(), behavior:'smooth'}));
    scroller?.addEventListener('scroll', update, {passive:true});
    window.addEventListener('resize', update);
    update();
})();
</script>
