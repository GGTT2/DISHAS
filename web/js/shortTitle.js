function shortTitle(title) {
    if (title.length > 30) {
        title = title.substr(0, 30) + '...';
    }
    return title;
}