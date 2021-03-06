
var commonInclude = angular.module("COMMONINCLUDE", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
commonInclude.constant("resourceDefinitions", {
    tsnwUser: {
        USERID: 1,
        NOME: "x",
        COGNOME: "x",
        EMAIL: "x"
    },
    lead: {
        piva: "x",
        lot: 1,
        pipe: 1,
        worker: 1,
        tmklock: 1,
        Punteggio_Azienda: 1,
        working: 1,
        donotcallbefore: new Date(),
        loc: "x",
        insert_timestamp: new Date(),
        unlock_timestamp: new Date(),
        static_lock: 1,
        force: 1,
        timestamp: new Date(),
        internal_id: 1,
        PartitaIva: "x",
        RagioneSociale: "x",
        Indirizzo: "x",
        Localita: "x",
        Cap: "x",
        Provincia_Sigla: "x",
        Provincia: "x",
        Regione: "x",
        Telefono: "x",
        SedePrincipale: "x",
        Email: "x",
        HomePage: "x",
        DataCostituzione: "x",
        FormaGiuridica: "x",
        CodiceFiscale: "x",
        CodiceAteco: "475210",
        FasciaFatturato: 1,
        ClasseDipendenti: 1,
        Attivita_ATECO: "x",
        Source: "x",
        Esclusione: 1
    },
    aziendaPerSpecchiettoTmk: {
        Punteggio: 1,
        AttivitaAteco: "x",
        Cap: "x",
        ClasseDipendenti: 1,
        DataCostituzione: "x",
        Email: "x",
        FasciaFatturato: 1,
        FormaGiuridica: "x",
        Indirizzo: "x",
        Localita: "x",
        PartitaIva: "x",
        Provincia: "x",
        RagioneSociale: "x",
        Regione: "x",
        SedePrincipale: "x",
        Sito: "x",
        VT: {}
    },
    leadPerInventario: {
        piva: "x",
        lot: 1,
        pipe: 1,
        worker: 1,
        tmklock: 1,
        punteggio_azienda: 1,
        working: 1,
        donotcallbefore: new Date(),
        loc: "x",
        insert_timestamp: new Date(),
        piva_lock: {
            piva: "x",
            unlock_timestamp: new Date(),
            static_lock: 1,
            force: 1,
            timestamp: new Date()
        },
        lead: {
            internal_id: 1,
            partitaiva: "x",
            ragionesociale: "x",
            indirizzo: "x",
            localita: "x",
            cap: "x",
            provincia_sigla: "x",
            provincia: "x",
            regione: "x",
            telefono: "x",
            sedeprincipale_im: "x",
            sedi_im: 1,
            email_ai: "",
            homepage_im: "x",
            numerocciaa_ai: "x",
            datacostituzione_ai: "x",
            formagiuridica: "x",
            codicefiscale: "x",
            numerorea: "x",
            datachiusuraultimobilancio_ai: "x",
            periodocompetenzaultimoanno_ai: "x",
            codiceateco: "x",
            codiceistatcomune_im: "x",
            classedemocomune_im: "x",
            fasciafatturato: "x",
            classedipendenti: "x",
            tipomercato_im: "x",
            import_im: "x",
            export_im: "x",
            attivita_ateco: "x",
            source: "x",
            punteggio_azienda: 1,
            esclusione: 1
        }
    }
});
commonInclude.constant("resourceRepresentations", {
    repLeadSpecchiettoAzienda: {
        USERID: 1,
        NOME: "x",
        COGNOME: "x",
        EMAIL: "x"
    }
});