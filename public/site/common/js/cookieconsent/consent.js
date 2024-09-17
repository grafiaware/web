import 'https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.0.1/dist/cookieconsent.umd.js';

/**
 * All config. options available here:
 * https://cookieconsent.orestbida.com/reference/configuration-reference.html
 */
CookieConsent.run({

    // root: 'body',
    // autoShow: true,
    // disablePageInteraction: true,
    // hideFromBots: true,
    // mode: 'opt-in',
    // revision: 0,
    
    
    revision: 0,
    cookie: {
        name: 'cc_cookie',
        // domain: location.hostname,
        // path: '/',
        // sameSite: "Lax",
        // expiresAfterDays: 365,
    },

    // https://cookieconsent.orestbida.com/reference/configuration-reference.html#guioptions
    guiOptions: {
        consentModal: {
            layout: 'cloud inline',
            position: 'bottom center',
            equalWeightButtons: true,
            flipButtons: false
        },
        preferencesModal: {
            layout: 'box',
            equalWeightButtons: true,
            flipButtons: false
        }
    },

    onFirstConsent: ({cookie}) => {
        console.log('onFirstConsent fired',cookie);
        logConsent();
    },

    onConsent: ({cookie}) => {
        console.log('onConsent fired!', cookie)
    },

    onChange: ({changedCategories, changedServices}) => {
        console.log('onChange fired!', changedCategories, changedServices);
        logConsent();
    },

    onModalReady: ({modalName}) => {
        console.log('ready:', modalName);
    },

    onModalShow: ({modalName}) => {
        console.log('visible:', modalName);
    },

    onModalHide: ({modalName}) => {
        console.log('hidden:', modalName);
    },

    categories: {
        necessary: {
            enabled: true,  // this category is enabled by default
            readOnly: true  // this category cannot be disabled
        },
        analytics: {
            autoClear: {
                cookies: [
                    {
                        name: /^_ga/,   // regex: match all cookies starting with '_ga'
                    },
                    {
                        name: '_gid',   // string: exact cookie name
                    },
                    {
                        name: /^www_/,   // regex: match all cookies starting with 'www_'
                    },                    
                ]
            },

            // https://cookieconsent.orestbida.com/reference/configuration-reference.html#category-services
            services: {
                ga: {
                    label: 'Google Analytics',
                    onAccept: () => {},
                    onReject: () => {}
                },
//                youtube: {
//                    label: 'Youtube Embed',
//                    onAccept: () => {},
//                    onReject: () => {}
//                },
            }
        },
        ads: {}
    },

    language: {
        default: 'cs',
        translations: {
            en: {
                consentModal: {
                    title: 'We use cookies',
                    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua',
                    acceptAllBtn: 'Accept all',
                    acceptNecessaryBtn: 'Reject all',
                    showPreferencesBtn: 'Manage Individual preferences',
                    // closeIconLabel: 'Reject all and close modal',
                    footer: `
                        <a href="#path-to-impressum.html" target="_blank">Impressum</a>
                        <a href="#path-to-privacy-policy.html" target="_blank">Privacy Policy</a>
                    `,
                },
                preferencesModal: {
                    title: 'Manage cookie preferences',
                    acceptAllBtn: 'Accept all',
                    acceptNecessaryBtn: 'Reject all',
                    savePreferencesBtn: 'Accept current selection',
                    closeIconLabel: 'Close modal',
                    serviceCounterLabel: 'Service|Services',
                    sections: [
                        {
                            title: 'Your Privacy Choices',
                            description: `In this panel you can express some preferences related to the processing of your personal information. You may review and change expressed choices at any time by resurfacing this panel via the provided link. To deny your consent to the specific processing activities described below, switch the toggles to off or use the “Reject all” button and confirm you want to save your choices.`,
                        },
                        {
                            title: 'Strictly Necessary',
                            description: 'These cookies are essential for the proper functioning of the website and cannot be disabled.',

                            //this field will generate a toggle linked to the 'necessary' category
                            linkedCategory: 'necessary'
                        },
                        {
                            title: 'Performance and Analytics',
                            description: 'These cookies collect information about how you use our website. All of the data is anonymized and cannot be used to identify you.',
                            linkedCategory: 'analytics',
                            cookieTable: {
                                caption: 'Cookie table',
                                headers: {
                                    name: 'Cookie',
                                    domain: 'Domain',
                                    desc: 'Description'
                                },
                                body: [
                                    {
                                        name: '_ga',
                                        domain: location.hostname,
                                        desc: 'Description 1',
                                    },
                                    {
                                        name: '_gid',
                                        domain: location.hostname,
                                        desc: 'Description 2',
                                    }
                                ]
                            }
                        },
                        {
                            title: 'Targeting and Advertising',
                            description: 'These cookies are used to make advertising messages more relevant to you and your interests. The intention is to display ads that are relevant and engaging for the individual user and thereby more valuable for publishers and third party advertisers.',
                            linkedCategory: 'ads',
                        },
                        {
                            title: 'More information',
                            description: 'For any queries in relation to my policy on cookies and your choices, please <a href="#contact-page">contact us</a>'
                        }
                    ]
                }
            },
        cs: {
                consentModal: {
                    title: 'Používáme cookies',
                    description: 'Soubory cookie používáme k analýze údajů o našich návštěvnících, ke zlepšení našich webových stránek, zobrazení personalizovaného obsahu a k tomu, abychom vám poskytli skvělý zážitek z webu. ',
                    acceptAllBtn: 'Přijmout všechny',
                    acceptNecessaryBtn: 'Odmítnout všechny',
                    showPreferencesBtn: 'Podrobné nastavení',
                    // closeIconLabel: 'Reject all and close modal',
                    footer: `
                        <a href="#path-to-impressum.html" target="_blank">Impressum</a>
                        <a href="#path-to-privacy-policy.html" target="_blank">Zásady ochrany osobních údajů</a>
                    `,
                },
                // zásady ochrany ou: https://commission.europa.eu/resources-partners/europa-web-guide_en
                preferencesModal: {
                    title: 'Správa předvoleb souborů cookie',
                    acceptAllBtn: 'Přijmout všechny',
                    acceptNecessaryBtn: 'Odmítnout všechny',
                    savePreferencesBtn: 'Přijmout aktuální výběr',
                    closeIconLabel: 'Zavřít modální okno',
                    serviceCounterLabel: 'Služby',
                    sections: [
                        {
                            title: 'Vaše volby ochrany osobních údajů',
                            description: `Na tomto panelu můžete zvolit některé preference týkající se zpracování vašich osobních údajů. Vaše volby můžete kdykoli zkontrolovat a změnit tak, že tento panel znovu vyvoláte prostřednictvím uvedeného odkazu. Chcete-li odmítnout svůj souhlas s konkrétními činnostmi zpracování popsanými níže, přepněte přepínače na vypnuto nebo použijte tlačítko "Odmítnout vše" a potvrďte, že chcete své volby uložit.`,
                        },
                        {
                            title: 'Nezbytně nutné soubory cookie',
                            description: 'Tyto soubory cookie jsou nezbytné pro správné fungování webových stránek a nelze je zakázat..',

                            //this field will generate a toggle linked to the 'necessary' category
                            linkedCategory: 'necessary'
                        },
                        {
                            title: 'Výkon a analytika',
                            description: 'Tyto soubory cookie shromažďují informace o tom, jak rychle naše stránky pracují a jak naše webové stránky používáte. Všechny údaje jsou anonymizované a nelze je použít k vaší identifikaci..',
                            linkedCategory: 'analytics',
                            cookieTable: {
                                caption: 'Tabulka cookie',
                                headers: {
                                    name: 'Cookie',
                                    domain: 'Domain',
                                    desc: 'Popis'
                                },
                                body: [
                                    {
                                        name: '_ga',
                                        domain: location.hostname,
                                        desc: 'Tyto cookie se používají pro Google Analytics k rozlišení jedinečných uživatelů přiřazením náhodně vygenerovaného čísla jako identifikátoru klienta a ke snížení počtu požadavků.'
                                    },
                                    {
                                        name: '_gid',
                                        domain: location.hostname,
                                        desc: 'Tato cookie se používá ve službě Google Analytics k ukládání a aktualizaci jedinečné hodnoty pro každou navštívenou stránku.'
                                    }
                                ]
                            }
                        },
                        {
                            title: 'Cílení a reklama',
                            description: 'Tyto soubory cookie se používají k tomu, aby reklamní sdělení byla relevantnější pro vás a vaše zájmy. Záměrem je zobrazovat reklamy, které jsou relevantní a zajímavé pro jednotlivé uživatele, a tím hodnotnější pro vydavatele a inzerenty třetích stran.',
                            linkedCategory: 'ads'
                        },
                        {
                            title: 'Více informací',
                            description: 'V případě jakýchkoli dotazů týkajících se našich zásad ohledně souborů cookie a vašich voleb, prosím <a href="#footer">kontaktujte nás</a>.'
                        }
                    ]
                }
            }
        }
    }
});
// base url
function getBaseUrl() {
    var pathparts = window.location.pathname.split('/');
    if (window.location.host === 'localhost') {
        var url = window.location.origin+'/'+pathparts[1].trim('/')+'/'; // http://localhost/myproject/
    }else{
        var url = window.location.origin;
    }
    return url;
}

function logConsent(){
    var base_url = getBaseUrl();   //window.location.origin;
    // Retrieve all the fields
    const cookie = CookieConsent.getCookie();
    const preferences = CookieConsent.getUserPreferences();

    const userConsent = {
        revision: cookie.revision,
        consentId: cookie.consentId,
        consentTimestamp: cookie.consentTimestamp,
        lastConsentTimestamp: cookie.lastConsentTimestamp,
        acceptType: preferences.acceptType,
        acceptedCategories: preferences.acceptedCategories,
        rejectedCategories: preferences.rejectedCategories,
        acceptedServices: preferences.acceptedServices,
        rejectedServices: preferences.rejectedServices
    };

    // Send the data to your backend
    // replace "/your-endpoint-url" with your API
    fetch('/'+base_url+'/consent/v1/log', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(userConsent)
    });
}
