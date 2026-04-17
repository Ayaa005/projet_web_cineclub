function filterMovies(){const v=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('.vcard').forEach(c=>{c.style.display=c.dataset.title.includes(v)?'':'none'});}
function openSuggest(){resetForm();document.getElementById('m-suggest').style.display='flex';}
function closeSuggest(){document.getElementById('m-suggest').style.display='none';resetForm();}
function resetForm(){
    document.getElementById('suggest-form').reset();
    document.getElementById('f-title').value='';
    document.getElementById('f-year').value='';
    document.getElementById('f-poster').value='uploads/posters/default.png';
    document.getElementById('tmdb-q').value='';
    document.getElementById('tmdb-drop').innerHTML='';
    document.getElementById('tmdb-drop').style.display='none';
    document.getElementById('poster-preview').style.display='none';
    document.getElementById('poster-img').src='';
}
let tmdbTimer;
function searchTMDB(q){
    clearTimeout(tmdbTimer);const drop=document.getElementById('tmdb-drop');
    if(q.length<2){drop.style.display='none';return;}
    tmdbTimer=setTimeout(async()=>{
        const KEY='<?=TMDB_KEY?>';
        if(KEY==='METS_TA_CLE_TMDB_ICI'){document.getElementById('f-title').value=q;drop.style.display='none';return;}
        try{const r=await fetch(`https://api.themoviedb.org/3/search/movie?api_key=${KEY}&query=${encodeURIComponent(q)}&language=fr-FR`);const d=await r.json();showDrop(d.results?.slice(0,6)||[]);}catch(e){drop.style.display='none';}
    },350);
}
function showDrop(results){
    const drop=document.getElementById('tmdb-drop');
    if(!results.length){drop.style.display='none';return;}
    drop.innerHTML=results.map(m=>`<div class="tmdb-item" onclick='selectMovie(${JSON.stringify(m).replace(/'/g,"&#39;")})'>
        <img src="${m.poster_path?'https://image.tmdb.org/t/p/w92'+m.poster_path:'/cineclub/uploads/posters/default.png'}" onerror="this.src='/cineclub/uploads/posters/default.png'">
        <div class="tmdb-item-info"><strong>${m.title}</strong><span>${m.release_date?m.release_date.substring(0,4):''}</span></div></div>`).join('');
    drop.style.display='block';
}
function selectMovie(m){
    document.getElementById('f-title').value=m.title;
    document.getElementById('f-year').value=m.release_date?m.release_date.substring(0,4):'';
    document.getElementById('tmdb-q').value=m.title;
    document.getElementById('tmdb-drop').style.display='none';
    if(m.poster_path){
        const url='https://image.tmdb.org/t/p/w500'+m.poster_path;
        document.getElementById('poster-img').src=url;
        document.getElementById('poster-preview').style.display='flex';
        document.getElementById('f-poster').value='tmdb:'+url;
    }
}
document.addEventListener('click',e=>{if(!e.target.closest('#tmdb-q')&&!e.target.closest('#tmdb-drop'))document.getElementById('tmdb-drop').style.display='none';});