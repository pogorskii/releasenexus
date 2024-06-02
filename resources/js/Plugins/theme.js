const passThrough = {
    autocomplete: {
        root: {class: "w-full bg-ph-teal-50"},
        container: {class: "w-full bg-ph-teal-50"},
        input: {class: "w-full bg-ph-teal-50"},
        token: {class: "bg-ph-teal-500 text-white"},
    },
    button: {
        root: "font-bold",
    },
    card: {
        title: "font-display",
    },
    chip: {
        root: {class: "bg-ph-teal-500 text-white"},
    },
    dataTable: {
        wrapper: {class: "p-card mb-6"},
        table: {class: "table text--sm"},
        paginator: {
            wrapper: {class: "border-0"},
            root: {class: "bg-transparent px-0"},
            current: {class: "mr--auto"},
            firstPageButton: {class: "ml-auto"},
            rowPerPageDropdown: {root: {class: "ml-0"}},
        },
        header: {class: "bg-transparent border-0 px-0"},
        column: {
            columnFilter: {class: "ml-2"},
        },
    },
    dialog: {
        content: {class: "grow flex flex-col"},
    },
    dropdown: {
        root: {class: "w-full bg-ph-teal-50"},
    },
    // editor: {
    //     content: {class: "prose !w-full !max-w-full"},
    // },
    input: {
        class: "w-full bg-zinc-100",
    },
    inputtext: {
        root: {class: "w-full bg-ph-teal-50"},
    },
    menu: {
        action: {class: "px-3 py-4"},
    },
    textarea: {
        root: {class: "w-full bg-ph-teal-50"},
    },
};

export default passThrough;
