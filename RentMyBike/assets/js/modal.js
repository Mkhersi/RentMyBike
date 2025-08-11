(function(){
  function confirmModal(message){
    return new Promise(resolve=>{
      const wrap = document.createElement('div');
      wrap.innerHTML = `
        <div class="modal-backdrop"></div>
        <div class="modal" role="dialog" aria-modal="true" aria-label="Confirmation">
          <p>${message}</p>
          <div class="actions">
            <button class="btn btn-danger" data-act="yes">Yes</button>
            <button class="btn btn-secondary" data-act="no">No</button>
          </div>
        </div>`;
      document.body.appendChild(wrap);
      wrap.addEventListener('click', (e)=>{
        if (e.target.dataset.act === 'yes') { cleanup(); resolve(true); }
        if (e.target.dataset.act === 'no' || e.target.classList.contains('modal-backdrop')) { cleanup(); resolve(false); }
      });
      function cleanup(){ wrap.remove(); }
    });
  }
  window.RMB = window.RMB || {};
  window.RMB.confirm = confirmModal;
})();
