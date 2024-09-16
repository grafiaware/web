import 'https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.0.1/dist/cookieconsent.umd.js';

/**
 * All config. options available here:
 * https://cookieconsent.orestbida.com/reference/configuration-reference.html
 */
CookieConsent.run({

    categories: {
        necessary: {
            enabled: true,  // this category is enabled by default
            readOnly: true  // this category cannot be disabled
        },
        analytics: {}
    },

    language: {
        default: 'cs',
        translations: {
            en: {
                consentModal: {
                    title: 'We use cookies',
                    description: 'Cookie modal description',
                    acceptAllBtn: 'Accept all',
                    acceptNecessaryBtn: 'Reject all',
                    showPreferencesBtn: 'Manage Individual preferences'
                },
                preferencesModal: {
                    title: 'Manage cookie preferences',
                    acceptAllBtn: 'Accept all',
                    acceptNecessaryBtn: 'Reject all',
                    savePreferencesBtn: 'Accept current selection',
                    closeIconLabel: 'Close modal',
                    sections: [
                        {
                            title: 'Somebody said ... cookies?',
                            description: 'I want one!'
                        },
                        {
                            title: 'Strictly Necessary cookies',
                            description: 'These cookies are essential for the proper functioning of the website and cannot be disabled.',

                            //this field will generate a toggle linked to the 'necessary' category
                            linkedCategory: 'necessary'
                        },
                        {
                            title: 'Performance and Analytics',
                            description: 'These cookies collect information about how you use our website. All of the data is anonymized and cannot be used to identify you.',
                            linkedCategory: 'analytics'
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
                    title: 'Používáme soubory cookie',
                    description: 'Modální popis souborů cookie',
                    acceptAllBtn: 'Přijmout všechny',
                    acceptNecessaryBtn: 'Odmítnout všechny',
                    showPreferencesBtn: 'Správa individuálních preferencí'
                },
                preferencesModal: {
                    title: 'Správa předvoleb souborů cookie',
                    acceptAllBtn: 'Přijmout všechny',
                    acceptNecessaryBtn: 'Odmítnout všechny',
                    savePreferencesBtn: 'Přijmout aktuální výběr',
                    closeIconLabel: 'Zavřít modální okno',
                    sections: [
                        {
                            title: 'Někdo řekl ... sušenky?',
                            description: 'Chci jednu!'
                        },
                        {
                            title: 'Nezbytně nutné soubory cookie',
                            description: 'Tyto soubory cookie jsou nezbytné pro správné fungování webových stránek a nelze je zakázat..',

                            //this field will generate a toggle linked to the 'necessary' category
                            linkedCategory: 'necessary'
                        },
                        {
                            title: 'Výkon a analytika',
                            description: 'Tyto soubory cookie shromažďují informace o tom, jak používáte naše webové stránky. Všechny údaje jsou anonymizované a nelze je použít k vaší identifikaci..',
                            linkedCategory: 'analytics'
                        },
                        {
                            title: 'Více informací',
                            description: 'V případě jakýchkoli dotazů týkajících se mých zásad ohledně souborů cookie a vašich voleb, prosím <a href="#contact-page">kontaktujte nás</a>.'
                        }
                    ]
                }
            }            
        }
    }
});
