<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Reference codes from Ringkasan Interaksi Obat PrEP.xlsx.
$config['interaksi_obat_referensi'] = array(
    'S1' => array('judul' => 'WHO. WHO implementation tool for pre-exposure prophylaxis of HIV infection: provider module for oral and long-acting PrEP. Geneva: WHO; 2024.', 'kegunaan' => 'Regimen oral PrEP TDF/3TC, hormon dan monitoring.', 'url' => 'https://www.who.int/publications/i/item/9789240097230'),
    'S2' => array('judul' => 'NIH. Guidelines for the Use of Antiretroviral Agents in Adults and Adolescents With HIV: Drug Interactions—NRTIs and Other Drugs.', 'kegunaan' => 'Interaksi NRTI, TDF/3TC dan rifamisin.', 'url' => 'https://clinicalinfo.hiv.gov/en/guidelines/hiv-clinical-guidelines-adult-and-adolescent-arv/drug-interactions-nrti'),
    'S3' => array('judul' => 'University of Liverpool. HIV Drug Interactions Checker. Accessed 7 Nov 2026.', 'kegunaan' => 'Pemeriksaan interaksi spesifik per pasangan obat.', 'url' => 'https://www.hiv-druginteractions.org/checker'),
    'S4' => array('judul' => 'WHO. Consolidated guidelines on tuberculosis: tuberculosis preventive treatment. Latest updates.', 'kegunaan' => 'Paduan TPT dan rifamisin/isoniazid.', 'url' => 'https://www.who.int/teams/global-tuberculosis-programme'),
    'S5' => array('judul' => 'CDC. Clinical Guidance for PrEP. HIV Nexus.', 'kegunaan' => 'Pemantauan PrEP, fungsi ginjal dan hepatitis B.', 'url' => 'https://www.cdc.gov/hivnexus/hcp/prep/'),
    'S6' => array('judul' => 'NIH. Guidelines for the Prevention and Treatment of Opportunistic Infections in Adults and Adolescents With HIV.', 'kegunaan' => 'Obat infeksi oportunistik dan antijamur.', 'url' => 'https://clinicalinfo.hiv.gov/en/guidelines/hiv-clinical-guidelines-adult-and-adolescent-opportunistic-infections'),
    'S7' => array('judul' => 'Viread (tenofovir disoproxil fumarate) and Epivir (lamivudine) prescribing information.', 'kegunaan' => 'Eliminasi renal, toksisitas ginjal dan penyesuaian dosis.', 'url' => 'https://www.gilead.com/-/media/files/pdfs/medicines/hiv/viread/viread_pi.pdf'),
    'S8' => array('judul' => 'University of Liverpool. HEP Drug Interactions Checker.', 'kegunaan' => 'Interaksi antivirus hepatitis C.', 'url' => 'https://www.hep-druginteractions.org/checker'),
    'S9' => array('judul' => 'NHS. Metronidazole: advice about alcohol. Follow local product label.', 'kegunaan' => 'Konseling alkohol selama metronidazol.', 'url' => 'https://www.nhs.uk/medicines/metronidazole/'),
    'S10' => array('judul' => 'WHO. Medical eligibility criteria for contraceptive use. 6th ed. Geneva: WHO; 2025.', 'kegunaan' => 'Interaksi/kelayakan kontrasepsi hormonal.', 'url' => 'https://www.who.int/publications/i/item/9789240115583'),
    'S11' => array('judul' => 'WHO. Family Planning: A Global Handbook for Providers.', 'kegunaan' => 'Konseling kontrasepsi dan rifamisin.', 'url' => 'https://www.who.int/publications/i/item/9780999203705'),
    'S12' => array('judul' => 'Hiransuthikul A, et al. Drug-drug interactions between feminizing hormone therapy and pre-exposure prophylaxis among transgender women: the iFACT study. J Int AIDS Soc. 2019;22(8):e25338.', 'kegunaan' => 'Farmakokinetik estradiol dan tenofovir pada PrEP.', 'url' => 'https://pmc.ncbi.nlm.nih.gov/articles/PMC6625338/'),
    'S13' => array('judul' => 'WHO. Alcohol fact sheet.', 'kegunaan' => 'Risiko alkohol umum; bukan bukti interaksi PK spesifik TDF/3TC.', 'url' => 'https://www.who.int/news-room/fact-sheets/detail/alcohol'),
);
