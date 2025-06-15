<?php 

return [
    // All available modules in the system
    'modules' => [
        // Setup Modules
        'users' => [
            'name' => 'User Management',
            'description' => 'Manage users and authentication',
            'routes' => ['users.*', 'auth.*'],
        ],
        'roles' => [
            'name' => 'Role Management',
            'description' => 'Manage user roles and permissions',
            'routes' => ['roles.*', 'permissions.*'],
        ],
        'warehouse' => [
            'name' => 'Warehouse Setup',
            'description' => 'Warehouse configuration',
            'routes' => ['warehouses.*', 'setup.warehouses.*'],
        ],
        'customers' => [
            'name' => 'Customer Management',
            'description' => 'Customer information management',
            'routes' => ['customers.*', 'setup.customers.*'],
        ],
        'suppliers' => [
            'name' => 'Supplier Management',
            'description' => 'Supplier information management',
            'routes' => ['suppliers.*', 'setup.suppliers.*'],
        ],
        'banks' => [
            'name' => 'Bank Setup',
            'description' => 'Bank account configuration',
            'routes' => ['banks.*', 'setup.banks.*'],
        ],
        'external_services' => [
            'name' => 'External Services',
            'description' => 'Third-party service configuration',
            'routes' => ['external-services.*', 'setup.external-services.*'],
        ],

        // Inventory Modules
        'products' => [
            'name' => 'Product Management',
            'description' => 'Product catalog and inventory',
            'routes' => ['products.*', 'setup.products.*'],
        ],
        'ingredient_groups' => [
            'name' => 'Ingredient Groups',
            'description' => 'Product ingredient grouping',
            'routes' => ['ingredient-groups.*', 'setup.ingredient-groups.*'],
        ],
        'purchase' => [
            'name' => 'Purchase Management',
            'description' => 'Purchase orders and receiving',
            'routes' => ['purchases.*', 'transaction.purchases.*'],
        ],
        'purchase_payments' => [
            'name' => 'Purchase Payments',
            'description' => 'Payment processing for purchases',
            'routes' => ['purchase-payments.*', 'transaction.purchase-payments.*'],
        ],
        'purchase_returns' => [
            'name' => 'Purchase Returns',
            'description' => 'Return goods to suppliers',
            'routes' => ['purchase-returns.*', 'transaction.purchase-returns.*'],
        ],
        'stock_transfer' => [
            'name' => 'Stock Transfer',
            'description' => 'Transfer stock between warehouses',
            'routes' => ['stock-transfers.*', 'transaction.stock-transfers.*'],
        ],
        'stock_manage' => [
            'name' => 'Stock Management',
            'description' => 'Stock adjustments and management',
            'routes' => ['stock-manage.*', 'transaction.stock-manage.*'],
        ],
        'supplier_due' => [
            'name' => 'Supplier Dues',
            'description' => 'Track supplier outstanding amounts',
            'routes' => ['supplier-dues.*', 'transaction.supplier-dues.*'],
        ],

        // Sales Modules
        'invoices' => [
            'name' => 'Invoice Management',
            'description' => 'Sales invoicing system',
            'routes' => ['invoices.*', 'transaction.invoices.*'],
        ],
        'invoice_payments' => [
            'name' => 'Invoice Payments',
            'description' => 'Customer payment processing',
            'routes' => ['invoice-payments.*', 'transaction.invoice-payments.*'],
        ],
        'invoice_dues' => [
            'name' => 'Invoice Dues',
            'description' => 'Customer outstanding amounts',
            'routes' => ['invoice-dues.*', 'transaction.invoice-dues.*'],
        ],
        'dishes_cups' => [
            'name' => 'Dishes & Cups',
            'description' => 'Food item and beverage management',
            'routes' => ['dishes.*', 'cups.*', 'setup.dishes.*'],
        ],
        'invoice_services' => [
            'name' => 'Invoice Services',
            'description' => 'Additional services in invoices',
            'routes' => ['invoice-services.*', 'setup.invoice-services.*'],
        ],

        // Expense Modules
        'expense_types' => [
            'name' => 'Expense Types',
            'description' => 'Expense item categories',
            'routes' => ['expense-types.*', 'setup.expense-types.*'],
        ],
        'expense_articles' => [
            'name' => 'Expense Articles',
            'description' => 'Expense item categories',
            'routes' => ['expense-articles.*', 'setup.expense-articles.*'],
        ],
        'expenses' => [
            'name' => 'Expense Management',
            'description' => 'Track business expenses',
            'routes' => ['expenses.*', 'transaction.expenses.*'],
        ],
        'expense_payments' => [
            'name' => 'Expense Payments',
            'description' => 'Process expense payments',
            'routes' => ['expense-payments.*', 'transaction.expense-payments.*'],
        ],

        // Banking Modules
        'bank_transfers' => [
            'name' => 'Bank Transfers',
            'description' => 'Transfer money between accounts',
            'routes' => ['bank-transfers.*', 'transaction.bank-transfers.*'],
        ],

        // Gaming Modules
        'cashin' => [
            'name' => 'Cash In',
            'description' => 'Customer cash deposits for gaming',
            'routes' => ['cashin.*', 'transaction.cashin.*'],
        ],
        'cashout' => [
            'name' => 'Cash Out',
            'description' => 'Customer cash withdrawals from gaming',
            'routes' => ['cashout.*', 'transaction.cashout.*'],
        ],

        // Service Modules
        'service_payments' => [
            'name' => 'Service Payments',
            'description' => 'Payments for additional services',
            'routes' => ['service-payments.*', 'transaction.service-payments.*'],
        ],

        // Reporting Modules
        'reports_bank' => [
            'name' => 'Bank Reports',
            'description' => 'Banking transaction reports',
            'routes' => ['reports.bank.*'],
        ],
        'reports_customer' => [
            'name' => 'Customer Reports',
            'description' => 'Customer transaction history',
            'routes' => ['reports.customer.*'],
        ],
        'reports_supplier' => [
            'name' => 'Supplier Reports',
            'description' => 'Supplier transaction history',
            'routes' => ['reports.supplier.*'],
        ],
        'reports_expense' => [
            'name' => 'Expense Reports',
            'description' => 'Expense analysis and reports',
            'routes' => ['reports.expense.*'],
        ],
        'reports_sales' => [
            'name' => 'Sales Reports',
            'description' => 'Sales performance reports',
            'routes' => ['reports.sales.*'],
        ],
        'reports_stock' => [
            'name' => 'Stock Reports',
            'description' => 'Inventory and stock reports',
            'routes' => ['reports.stock.*'],
        ],
        'reports_profit' => [
            'name' => 'Profit Reports',
            'description' => 'Profit and loss statements',
            'routes' => ['reports.profit.*'],
        ],

        // History Modules
        'history_transactions' => [
            'name' => 'Transaction History',
            'description' => 'All transaction historical data',
            'routes' => ['history.*'],
        ],

        // Advanced Modules
        'system_settings' => [
            'name' => 'System Settings',
            'description' => 'Advanced system configuration',
            'routes' => ['system.*', 'settings.*'],
        ],
        'transaction_management' => [
            'name' => 'Transaction Management',
            'description' => 'Transaction update and delete history',
            'routes' => ['transaction-management.*', 'advanced.transactions.*'],
        ],
    ],

    // Feature definitions (collections of modules)
    'features' => [
        'setup' => [
            'name' => 'Setup Management',
            'description' => 'Basic setup and configuration',
            'modules' => ['users', 'roles', 'warehouse', 'customers', 'suppliers', 'banks', 'external_services'],
            'always_enabled' => true, // Always available
        ],
        
        'inventory' => [
            'name' => 'Inventory Management',
            'description' => 'Complete inventory and warehouse management',
            'modules' => [
                'products', 'ingredient_groups', 'purchase', 'purchase_payments', 
                'purchase_returns', 'stock_transfer', 'stock_manage', 'supplier_due'
            ],
        ],

        'sales' => [
            'name' => 'Sales Management',
            'description' => 'Sales, invoicing and customer management',
            'modules' => [
                'invoices', 'invoice_payments', 'invoice_dues', 
                'dishes_cups', 'invoice_services'
            ],
        ],

        'expense' => [
            'name' => 'Expense Management',
            'description' => 'Expense tracking and management',
            'modules' => ['expense_articles', 'expense_types', 'expenses', 'expense_payments'],
        ],

        'banking' => [
            'name' => 'Bank Management',
            'description' => 'Bank accounts and transfers',
            'modules' => ['bank_transfers'],
        ],

        'gaming' => [
            'name' => 'Gaming Management',
            'description' => 'Cash in/out for gaming services',
            'modules' => ['cashin', 'cashout'],
        ],

        'service' => [
            'name' => 'Service Management',
            'description' => 'Additional services management',
            'modules' => ['service_payments'],
        ],

        'reports' => [
            'name' => 'Reporting',
            'description' => 'Comprehensive reporting system',
            'modules' => [
                'reports_bank', 'reports_customer', 'reports_supplier', 
                'reports_expense', 'reports_sales', 'reports_stock', 'reports_profit'
            ],
        ],

        'history' => [
            'name' => 'Transaction History',
            'description' => 'Historical transaction data',
            'modules' => ['history_transactions'],
        ],

        'advanced' => [
            'name' => 'Advanced Features',
            'description' => 'System settings and transaction management',
            'modules' => ['system_settings', 'transaction_management'],
        ],
    ],

    // Tenant-specific configurations
    'tenants' => [
        'club.david-accounts-v3.xammp' => [
            'enabled_features' => ['setup', 'expense', 'banking'],
            'additional_modules' => ['reports_expense'], // Direct module assignment
            'disabled_modules' => [], // Override specific modules
            'notes' => 'Basic package + expense reports',
        ],
        
        'restaurant-xyz.com' => [
            'enabled_features' => ['setup', 'inventory', 'sales', 'expense', 'reports'],
            'additional_modules' => ['cashin'], // Special case: only cash-in from gaming
            'disabled_modules' => ['reports_profit'], // Remove profit reports
            'notes' => 'Full restaurant - no gaming except cash-in, no profit reports',
        ],
        
        'gaming-cafe.com' => [
            'enabled_features' => ['setup', 'inventory', 'sales', 'gaming', 'banking', 'reports'],
            'additional_modules' => [],
            'disabled_modules' => [],
            'notes' => 'Restaurant + Gaming cafe features',
        ],
        
        'warehouse-only.com' => [
            'enabled_features' => ['setup', 'inventory'],
            'additional_modules' => ['reports_stock', 'reports_supplier'],
            'disabled_modules' => ['purchase_returns'], // No returns allowed
            'notes' => 'Warehouse management + specific reports, no returns',
        ],

        'custom-client.com' => [
            'enabled_features' => ['setup'],
            'additional_modules' => [
                'products', 'purchase', 'expenses', 'reports_expense', 'reports_stock'
            ],
            'disabled_modules' => [],
            'notes' => 'Custom setup: only products, purchase, expenses with reports',
        ],
        
        'full-enterprise.com' => [
            'enabled_features' => [
                'setup', 'inventory', 'sales', 'expense', 'banking', 
                'gaming', 'service', 'reports', 'history', 'advanced'
            ],
            'additional_modules' => [],
            'disabled_modules' => [],
            'notes' => 'All features enabled',
        ],
    ],
];
