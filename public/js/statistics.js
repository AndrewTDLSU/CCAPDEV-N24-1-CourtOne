// Render server-provided statistics in the original chart containers.
const source = document.getElementById('venue-statistics');
if (source) {
  const data = JSON.parse(source.textContent);
  ['revyear','bookyear','courtyear','courtmonth'].forEach(id => {
    document.getElementById(id).addEventListener('change', event => {
      const url = new URL(window.location.href);
      url.searchParams.set(id === 'courtmonth' ? 'month' : 'year', event.target.value);
      window.location.assign(url);
    });
  });
  const colors = ['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#0dcaf0'];
  function chart(id, rows, key, type) {
    const canvas = document.getElementById(id);
    const draw = () => {
      const width = Math.max(220, canvas.parentElement.clientWidth), height = 210;
      canvas.width = width * devicePixelRatio; canvas.height = height * devicePixelRatio;
      canvas.style.width = '100%'; canvas.style.height = height + 'px';
      const ctx = canvas.getContext('2d'); ctx.scale(devicePixelRatio, devicePixelRatio);
      const values = rows.map(row => Number(row[key]));
      canvas.setAttribute('role','img'); canvas.setAttribute('aria-label', rows.map((row,i) => (row.courtName || 'Month ' + row.month) + ': ' + values[i]).join(', ') || 'No data');
      ctx.font = '11px Arial'; ctx.fillStyle = '#64748b';
      const total = values.reduce((a,b) => a+b,0);
      if (!total) {ctx.fillText('No data for this period',20,100); return;}
      if (type === 'pie') {
        let angle = -Math.PI/2;
        values.forEach((value,i) => {
          const end = angle + value/total * Math.PI*2;
          ctx.beginPath();ctx.moveTo(width/2,80);ctx.arc(width/2,80,65,angle,end);ctx.closePath();ctx.fillStyle=colors[i%colors.length];ctx.fill();angle=end;
        });
        ctx.fillStyle='#64748b';ctx.fillText(total+' completed bookings',20,180);
      } else {
        const max = Math.max(...values,1), step=(width-45)/values.length;
        ctx.fillText(max.toLocaleString(),0,15);
        ctx.strokeStyle='#dee2e6';ctx.beginPath();ctx.moveTo(30,15);ctx.lineTo(30,175);ctx.lineTo(width,175);ctx.stroke();
        ctx.strokeStyle=colors[0];ctx.lineWidth=2;ctx.beginPath();
        values.forEach((value,i) => {
          const x=35+i*step, y=175-value/max*150;
          ctx.fillStyle=colors[0];
          if(type==='bar')ctx.fillRect(x,y,Math.max(step-5,2),175-y);
          else if(i===0)ctx.moveTo(x,y);else ctx.lineTo(x,y);
          ctx.fillStyle='#64748b';ctx.fillText(String(rows[i].month),x,195);
        });
        if(type==='line')ctx.stroke();
      }
    };
    new ResizeObserver(draw).observe(canvas.parentElement); draw();
    const details=document.createElement('details'); const summary=document.createElement('summary');summary.textContent='View chart data';details.append(summary);
    rows.forEach(row=>{const line=document.createElement('p');line.className='small mb-1';line.textContent=(row.courtName||'Month '+row.month)+': '+Number(row[key]).toLocaleString();details.append(line);});
    canvas.parentElement.parentElement.after(details);
  }
  chart('revenueLineChart',data.revenue,'revenue','line');
  chart('bookingsBarGraph',data.bookings,'bookings','bar');
  chart('courtUsagePieChart',data.usage,'completedBookings','pie');
}
