function clean_text(text){
    let text_cleaning = text.toLowerCase();
    text_cleaning = text_cleaning.replaceAll("ü", "ue").replaceAll("ö", "oe").replaceAll("ä", "ae").replaceAll("ß", "ss");
    text_cleaning = text_cleaning.replaceAll(/[^a-z0-9]/g, " ");
    text_cleaning = text_cleaning.trim();
    return text_cleaning;
}
