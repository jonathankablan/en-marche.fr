import validateEmail from '../validator/emailValidator';
import formValidator from '../validator/formValidator';

export default (formType, autocomplete) => {
    const form = dom('form[name="adherent_registration"]') || dom('form[name="become_adherent"]');
    const emailField = dom('#adherent_registration_emailAddress_first');
    const confirmEmailField = dom('#adherent_registration_emailAddress_second');
    let zipCodeField = dom('#adherent_registration_address_postalCode');
    const captchaBlock = dom('div.g-recaptcha');

    if (!zipCodeField) {
        zipCodeField = dom('#become_adherent_address_postalCode');
    }

    /**
     * Display/hide the second email field according the value of first email field
     *
     * @param event
     */
    const checkEmail = (event) => {
        if (validateEmail(event.target.value)) {
            removeClass(confirmEmailField.parentElement, 'visually-hidden');
        } else {
            addClass(confirmEmailField.parentElement, 'visually-hidden');
        }
    };

    /**
     * Display captcha block when the ZipCode is filled and remove the listener from ZipCode field
     *
     * @param event
     */
    const displayCaptcha = (event) => {
        if (captchaBlock
            && event.target.value
            && -1 !== captchaBlock.parentElement.className.indexOf('visually-hidden')
        ) {
            removeClass(captchaBlock.parentElement, 'visually-hidden');
            off(zipCodeField, 'input', displayCaptcha);
        }
    };

    if (emailField) {
        on(emailField, 'input', checkEmail);
        emailField.dispatchEvent(new Event('input'));
    }

    on(zipCodeField, 'input', displayCaptcha);
    zipCodeField.dispatchEvent(new Event('input'));

    formValidator(formType, form);

    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                const geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                const circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy,
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }

    const componentForm = {
        street_number: {
            id: 'adherent_registration_address_address',
            type: 'long_name',
        },
        route: {
            id: 'adherent_registration_address_address',
            type: 'long_name',
        },
        locality: {
            id: 'adherent_registration_address_cityName',
            type: 'long_name',
        },
        postal_code: {
            id: 'adherent_registration_address_postalCode',
            type: 'long_name',
        },
        country: {
            id: 'adherent_registration_address_country',
            type: 'short_name',
        },
    };

    function fillInAddress() {
        let component;
        const place = autocomplete.getPlace();

        for (let i = 0; i < componentForm.length; i + 1) {
            const item = componentForm[i];
            document.getElementById(item.id).value = '';
            document.getElementById(item.id).disabled = false;
        }

        for (let i = 0; i < place.address_components.length; i + 1) {
            const addressType = place.address_components[i].types[0];
            component = componentForm[addressType];
            if (component) {
                const val = place.address_components[i][component.type];
                const domElement = document.getElementById(component.id);
                if (domElement.value) {
                    domElement.value += ' ';
                }
                domElement.value += val;
                domElement.dispatchEvent(new InputEvent('input'));
            }
        }
    }

    autocomplete.addListener('place_changed', fillInAddress);
    on(dom('#autocomplete'), 'focus', geolocate);
};
