function validateForm() {
    const adLink = document.querySelector('input[name="ad_link"]').value;
    const email = document.querySelector('input[name="email"]').value;
    const urlPattern = /^(https?:\/\/)?(www\.)?olx\.\S+$/i;
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!urlPattern.test(adLink)) {
        alert("Please enter a valid OLX ad link.");
        return false;
    }

    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }

    return true;
}