function downloadPdf() {
    const url = new window.URL(window.location.href);
    let origin = url.origin;
    let pathname = url.pathname;
    let search = url.search;
    const download = origin + pathname + '/download' + search;
    window.open(download, '_blank');
}