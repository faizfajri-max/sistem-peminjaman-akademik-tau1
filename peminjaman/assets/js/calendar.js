// Interactive Calendar for Jadwal & Ketersediaan
(function(){
  const DAYS = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

  function startOfMonth(d){ const x = new Date(d); x.setDate(1); x.setHours(0,0,0,0); return x }
  function endOfMonth(d){ const x = new Date(d); x.setMonth(x.getMonth()+1,0); x.setHours(23,59,59,999); return x }
  function addDays(d, n){ const x = new Date(d); x.setDate(x.getDate()+n); return x }
  function sameDay(a,b){ return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate() }

  function renderCalendar(el, opts){
    const state = {
      date: opts?.date ? new Date(opts.date) : new Date(),
      facilityId: opts?.facilityId || 'all',
    };

    const header = el.querySelector('.cal-header');
    const grid = el.querySelector('.cal-grid');
    const monthLabel = header.querySelector('[data-cal-month]');
    const prevBtn = header.querySelector('[data-cal-prev]');
    const nextBtn = header.querySelector('[data-cal-next]');
    const facilitySelect = el.querySelector('[data-cal-facility]');
    const detail = el.querySelector('[data-cal-detail]');

    // Populate facilities
    const facs = [{id:'all', name:'Semua Fasilitas'}, ...SPFK.getFacilities()];
    facilitySelect.innerHTML = facs.map(f=>`<option value="${f.id}">${f.name||f}</option>`).join('');
    facilitySelect.value = state.facilityId;

    function rangeDays(month){
      const s = startOfMonth(month); const e = endOfMonth(month);
      const firstIndex = (s.getDay()+0)%7; // start from Sunday
      const days = [];
      // fill from previous month
      for(let i=0;i<firstIndex;i++) days.push({date:addDays(s, i-firstIndex), muted:true});
      // current month
      for(let d=1; d<=e.getDate(); d++) days.push({date:new Date(month.getFullYear(), month.getMonth(), d)});
      // next month to fill grid to multiple of 7 (up to 42 cells)
      while(days.length%7!==0) days.push({date:addDays(e, days.length%7)});
      return days;
    }

    function cellStatus(date){
      const bookings = SPFK.getBookings().filter(b=>{
        if(state.facilityId!=='all' && b.facilityId!==state.facilityId) return false;
        const sd = new Date(b.startDate); const ed = new Date(b.endDate);
        const within = (date>=startOfDay(sd) && date<=startOfDay(ed));
        return within && (b.status!=='rejected');
      });
      return bookings.length>0 ? {busy:true, count:bookings.length} : {busy:false, count:0};
    }

    function startOfDay(d){ const x = new Date(d); x.setHours(0,0,0,0); return x }

    function render(){
      monthLabel.textContent = state.date.toLocaleDateString('id-ID',{month:'long', year:'numeric'});
      grid.innerHTML = '';
      // day names
      DAYS.forEach(n=>{
        const dn = document.createElement('div'); dn.className='day-name'; dn.textContent=n; grid.appendChild(dn);
      });
      const today = startOfDay(new Date());
      rangeDays(state.date).forEach(({date, muted})=>{
        const cell = document.createElement('div'); cell.className='cell'; if(muted) cell.classList.add('muted');
        const top = document.createElement('div'); top.className='date';
        top.textContent = date.getDate();
        if(sameDay(date, today)) top.style.color = 'var(--primary)';
        cell.appendChild(top);
        const stat = cellStatus(date);
        const slot = document.createElement('div'); slot.className='slot'; slot.textContent = stat.busy? `${stat.count} terpakai` : 'Tersedia';
        slot.classList.add(stat.busy? 'busy':'free');
        cell.appendChild(slot);
        cell.addEventListener('click', ()=> showDetail(date));
        grid.appendChild(cell);
      });
    }

    function showDetail(date){
      const facsById = Object.fromEntries(SPFK.getFacilities().map(f=>[f.id,f]));
      const bookings = SPFK.getBookings().filter(b=>{
        const sd = new Date(b.startDate); const ed = new Date(b.endDate);
        return (startOfDay(date)>=startOfDay(sd) && startOfDay(date)<=startOfDay(ed)) && (state.facilityId==='all' || b.facilityId===state.facilityId);
      });
      if(!bookings.length){ detail.innerHTML = `<div class="badge success">Semua slot tersedia pada ${SPFK.utils.fmtDate(date)}</div>`; return; }
      detail.innerHTML = `
        <div class="card">
          <div class="card-header"><strong>${SPFK.utils.fmtDate(date)}</strong> • ${bookings.length} peminjaman</div>
          <div class="card-body">
            <div class="grid">
            ${bookings.map(b=>`
              <div class="card" style="border-color:#FBE0B3;background:#FFFDF7">
                <div class="card-body">
                  <div style="display:flex;justify-content:space-between;gap:8px;align-items:center">
                    <div>
                      <div style="font-weight:600">${facsById[b.facilityId]?.name || b.facilityId}</div>
                      <div style="font-size:12px;color:var(--muted)">${SPFK.utils.fmtTime(b.startDate)} - ${SPFK.utils.fmtTime(b.endDate)} • ${b.purpose||'-'}</div>
                      <div style="font-size:12px;color:var(--muted)">Pemohon: ${b.requesterName} (${b.requesterEmail})</div>
                    </div>
                    <span class="badge ${b.status==='approved'?'success':(b.status==='pending'?'warn':'danger')}">${b.status}</span>
                  </div>
                </div>
              </div>
            `).join('')}
            </div>
          </div>
        </div>
      `;
    }

    prevBtn.addEventListener('click', ()=>{ state.date.setMonth(state.date.getMonth()-1); render(); });
    nextBtn.addEventListener('click', ()=>{ state.date.setMonth(state.date.getMonth()+1); render(); });
    facilitySelect.addEventListener('change', (e)=>{ state.facilityId = e.target.value; render(); detail.innerHTML=''; });

    render();
  }

  // expose
  window.initScheduleCalendar = renderCalendar;
})();

// Vue-based Schedule App for schedule.html template
(function(){
  if (!window.Vue) return;
  const root = document.getElementById('app-schedule');
  if (!root) return;

  const { createApp, ref, computed, onMounted } = Vue;

  function startOfDay(d){ const x = new Date(d); x.setHours(0,0,0,0); return x }
  function sameDay(a,b){ return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate() }
  function addDays(d, n){ const x = new Date(d); x.setDate(x.getDate()+n); return x }
  function endOfMonth(d){ const x = new Date(d); x.setMonth(x.getMonth()+1,0); x.setHours(23,59,59,999); return x }

  createApp({
    setup(){
      const view = ref('month');
      const current = ref(new Date());
      const selectedDate = ref(startOfDay(new Date()));
      const facilities = ref([]);
      const facilityId = ref('all');

      const dayNames = ref(['Min','Sen','Sel','Rab','Kam','Jum','Sab']);

      const monthLabel = computed(()=> current.value.toLocaleDateString('id-ID',{month:'long',year:'numeric'}));

      const monthDays = computed(()=>{
        const month = current.value;
        const first = new Date(month.getFullYear(), month.getMonth(), 1);
        const last = endOfMonth(month);
        const firstIndex = (first.getDay()+0)%7; // Minggu mulai
        const days = [];
        for(let i=0;i<firstIndex;i++) days.push({date: addDays(first, i-firstIndex), muted:true});
        for(let d=1; d<=last.getDate(); d++) days.push({date: new Date(month.getFullYear(), month.getMonth(), d), muted:false});
        while(days.length % 7 !== 0) days.push({date: addDays(last, days.length%7), muted:true});
        return days;
      });

      const weekDays = computed(()=>{
        // Mulai dari Minggu pada minggu dari current.value
        const base = new Date(current.value);
        const start = addDays(base, -base.getDay());
        return Array.from({length:7}, (_,i)=> addDays(start,i));
      });

      const borrowLink = computed(()=>{
        const d = selectedDate.value;
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth()+1).padStart(2,'0');
        const dd = String(d.getDate()).padStart(2,'0');
        return `borrow.html?date=${yyyy}-${mm}-${dd}`;
      });

      function isPast(d){ return startOfDay(d) < startOfDay(new Date()); }
      function isToday(d){ return sameDay(startOfDay(d), startOfDay(new Date())); }

      function getBookings(){
        try { return SPFK.getBookings() || []; } catch { return []; }
      }
      function filteredBookings(){
        const list = getBookings();
        if (facilityId.value==='all') return list.filter(b=> b.status !== 'rejected');
        return list.filter(b=> b.facilityId===facilityId.value && b.status !== 'rejected');
      }
      function onDate(date){
        const d0 = startOfDay(date);
        return filteredBookings().filter(b=>{
          const sd = startOfDay(new Date(b.startDate));
          const ed = startOfDay(new Date(b.endDate));
          return d0 >= sd && d0 <= ed;
        });
      }
      function busy(date){ return onDate(date).length > 0; }
      function countOn(date){ return onDate(date).length; }
      function eventsOn(date){ return onDate(date); }
      function timeRange(b){
        try {
          const s = new Date(b.startDate).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
          const e = new Date(b.endDate).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});
          return `${s} - ${e}`;
        } catch { return ''; }
      }
      function facName(id){
        const fac = facilities.value.find(f=>f.id===id);
        return fac ? fac.name : (id||'-');
      }
      function selectDate(d){ selectedDate.value = startOfDay(d); }
      function prev(){
        if (view.value==='month') {
          current.value = new Date(current.value.getFullYear(), current.value.getMonth()-1, 1);
        } else {
          current.value = addDays(current.value, -7);
        }
      }
      function next(){
        if (view.value==='month') {
          current.value = new Date(current.value.getFullYear(), current.value.getMonth()+1, 1);
        } else {
          current.value = addDays(current.value, 7);
        }
      }

      const selectedLabel = computed(()=> selectedDate.value.toLocaleDateString('id-ID',{weekday:'long', day:'2-digit', month:'long', year:'numeric'}));

      onMounted(()=>{
        try { facilities.value = SPFK.getFacilities() || []; } catch { facilities.value = []; }
        // Ensure v-cloak removed even if Vue doesn't handle it
        const el = document.getElementById('app-schedule');
        if (el && el.hasAttribute('v-cloak')) el.removeAttribute('v-cloak');
      });

      return { view, facilities, facilityId, dayNames, monthLabel, monthDays, weekDays, borrowLink, selectedDate, selectedLabel,
               isPast, isToday, busy, countOn, eventsOn, timeRange, facName, selectDate, prev, next };
    }
  }).mount('#app-schedule');
})();

// Fallback: if v-cloak still present after load, show a helpful message instead of blank
(function(){
  window.addEventListener('load', function(){
    setTimeout(function(){
      const el = document.getElementById('app-schedule');
      if (el && el.hasAttribute('v-cloak')){
        el.removeAttribute('v-cloak');
        el.innerHTML = '<div class="card" style="padding:16px;">Gagal memuat kalender. Silakan hard refresh (Ctrl+F5). Jika masih kosong, periksa Console (F12) dan beri tahu kami pesan errornya.</div>';
      }
    }, 1200);
  });
})();
