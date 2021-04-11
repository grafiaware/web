<?php

// template
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Pes\Text\Text;
/** @var PhpTemplateRendererInterface $this */

use Site\Configuration;

// visitor
use Model\Entity\LoginAggregateFullInterface;
use Model\Repository\StatusSecurityRepo;
use Model\Repository\VisitorDataRepo;


//   zkrácené url https://forms.gle/w5NTnXbxEg6GGRLp7
//    odkaz na formulář s předvyplněnými daty:
//   https://docs.google.com/forms/d/e/1FAIpQLSdupUpxH5KBKVaQoZzLWlzeX0jdp2OX25aRc5ar53CEfZTzcg/viewform?usp=pp_url&entry.190219785=P%C5%99%C3%ADjmen%C3%AD&entry.1783510966=Jm%C3%A9no&entry.1428572852=email&entry.1017719627=Nezam%C4%9Bstnan%C3%BD&entry.913798124=S%C5%A0/maturita&entry.11912052=r%C3%A1dio&entry.1958175446=chci+m%C3%ADt+p%C5%99ehled+o+trhu+pr%C3%A1ce&entry.742183994=LoginName

    $formUid = "1FAIpQLSdupUpxH5KBKVaQoZzLWlzeX0jdp2OX25aRc5ar53CEfZTzcg";

    $formUrlArray[] = 'https://docs.google.com/forms/d/e/';
    $formUrlArray[] = $formUid;
    $formUrlArray[] = '/viewform';
    $formUrlArray[] = '?';
    $formUrlArray[] = 'usp=pp_url';


//  <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSdupUpxH5KBKVaQoZzLWlzeX0jdp2OX25aRc5ar53CEfZTzcg/viewform?embedded=true" frameborder="0" id="gformQQQ" width=100% height="4000" frameborder="0" marginheight="0" marginwidth="0">Načítání…</iframe>

    $statusSecurityRepo = $container->get(StatusSecurityRepo::class);
    /** @var StatusSecurityRepo $statusSecurityRepo */
    $statusSecurity = $statusSecurityRepo->get();
    /** @var LoginAggregateFullInterface $loginAggregate */
    $loginAggregate = $statusSecurity->getLoginAggregate();

    if (isset($loginAggregate)) {
        $loginName = $loginAggregate->getLoginName();
        $role = $loginAggregate->getCredentials()->getRole() ?? '';
    }

    // poue pro default roli 'visitor'
    if (isset($role) AND $role==(Configuration::loginLogoutControler()['defaultRole'])) {
        $visitorDataRepo = $container->get(VisitorDataRepo::class);
        $visitorData = $visitorDataRepo->get($loginName);
    }

    if (isset($visitorData)) {
        $entries = [
            'embedded' => 'true',
    //        'usp' => 'pp_url',
            '190219785' => $visitorData->getSurname() ?? '',
            '1783510966' => $visitorData->getName() ?? '',
            '1428572852' => $visitorData->getEmail() ?? '',
            '742183994' => $visitorData->getLoginName() ?? '',
        ];
    } else {
        $entries = [
            'embedded' => 'true'
        ];
    }

    foreach ($entries as $key => $value) {
        $queryArray[] = 'entry.'.$key.'='.urlencode($value);
    }

    $src = implode('', $formUrlArray).implode('&', $queryArray);

?>
<div class="ui segment">
    <div class="paper editable">
        <section>
            <form>
                <headline class="ui header borderDance">
                    <p class="nadpis podtrzeny nastred nadpis-scroll show-on-scroll">Anketa a slosování</p>
                </headline>
            </form>
        </section>
        <section>
            <form>
                <perex class="borderDance">
            <?= Text::mono('<p class="text">Vyplňte ANKETU NÁVŠTĚVNÍKA</a> a zařaďte se do slosování o ceny, které proběhne 28. dubna!</p>'
            .(isset($visitorData) ? '' : Text::mono('<p class="text">Pokud jste zaregistrovaní návštěvníci veletrhu, nezapoměňte se předtím přihlásit!!</p>'))
            .'<p class="text okraje-vertical"></p>');
            ?>
                </perex>
            </form>
        </section>
        <section>
            <div class="">
                <div class="ui grid">
                    <div class="row">
                        <div class="sixteen wide column">
                            <iframe src="<?= $src ?>" frameborder="0" id="gformQQQ" width=100% height="4000" frameborder="0" marginheight="0" marginwidth="0">Načítání…</iframe>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>