const currencyFormatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',
  maximumFractionDigits: 2,
})

function formatMoney(value) {
  const amount = Number(value ?? 0)

  return currencyFormatter.format(Number.isNaN(amount) ? 0 : amount)
}

function joinClient(client) {
  if (!client) {
    return 'No client linked'
  }

  return `${client.name} ${client.lastname}`.trim()
}

function joinProduct(product) {
  return product?.name ?? 'No product linked'
}

function joinSupplier(supplier) {
  return supplier?.name ?? 'No supplier linked'
}

export const resourceConfigs = {
  products: {
    key: 'products',
    path: 'products',
    title: 'Products',
    singular: 'product',
    endpoint: '/api/v1/products',
    description: 'Maintain the catalog that feeds orders and supplier assignments.',
    intro:
      'Products are the center of the flow. From here you can prepare the records that later connect with orders and suppliers.',
    columns: [
      { label: 'Name', value: (record) => record.name },
      { label: 'Description', value: (record) => record.description },
      { label: 'Price', value: (record) => formatMoney(record.price) },
      { label: 'Quantity', value: (record) => record.quantity },
    ],
    fields: [
      { key: 'name', label: 'Name', type: 'text', placeholder: 'Warehouse notebook' },
      {
        key: 'description',
        label: 'Description',
        type: 'textarea',
        placeholder: 'Compact product description for the catalog.',
      },
      { key: 'price', label: 'Price', type: 'number', step: '0.01', min: 0 },
      { key: 'quantity', label: 'Quantity', type: 'number', step: '1', min: 0 },
    ],
  },
  suppliers: {
    key: 'suppliers',
    path: 'suppliers',
    title: 'Suppliers',
    singular: 'supplier',
    endpoint: '/api/v1/suppliers',
    description: 'Keep the supplier base simple and ready to map products later.',
    intro:
      'Suppliers are intentionally lightweight in this release so the assignment flow stays fast while you build the UI.',
    columns: [
      { label: 'Name', value: (record) => record.name },
      { label: 'Products linked', value: (record) => record.products?.length ?? 0 },
    ],
    fields: [{ key: 'name', label: 'Name', type: 'text', placeholder: 'Northwind Supply' }],
  },
  clients: {
    key: 'clients',
    path: 'clients',
    title: 'Clients',
    singular: 'client',
    endpoint: '/api/v1/clients',
    description: 'Manage the people or companies that own each order.',
    intro: 'Clients connect directly with orders, so this section becomes the anchor for operational tracking.',
    columns: [
      { label: 'Name', value: (record) => `${record.name} ${record.lastname}`.trim() },
      { label: 'Email', value: (record) => record.email },
    ],
    fields: [
      { key: 'name', label: 'Name', type: 'text', placeholder: 'Marina' },
      { key: 'lastname', label: 'Lastname', type: 'text', placeholder: 'Gonzalez' },
      { key: 'email', label: 'Email', type: 'email', placeholder: 'marina@example.com' },
    ],
  },
  orders: {
    key: 'orders',
    path: 'orders',
    title: 'Orders',
    singular: 'order',
    endpoint: '/api/v1/orders',
    description: 'Track every order with its client and current status identifier.',
    intro:
      'Orders already hydrate their client and detail relationships from the API, so the SPA can show richer operational context.',
    columns: [
      { label: 'Order number', value: (record) => record.order_number },
      { label: 'Status', value: (record) => record.status_id },
      { label: 'Client', value: (record) => joinClient(record.client) },
      { label: 'Details', value: (record) => record.order_details?.length ?? 0 },
    ],
    fields: [
      { key: 'order_number', label: 'Order number', type: 'text', placeholder: 'ORD-2026-001' },
      { key: 'status_id', label: 'Status ID', type: 'number', step: '1', min: 0, max: 255 },
      {
        key: 'client_id',
        label: 'Client',
        type: 'select',
        optionsResource: 'clients',
        optionLabel: (record) => `${record.name} ${record.lastname} (${record.email})`.trim(),
      },
    ],
  },
  'order-details': {
    key: 'order-details',
    path: 'order-details',
    title: 'Order details',
    singular: 'order detail',
    endpoint: '/api/v1/order-details',
    description: 'Represent the product lines that make each order meaningful.',
    intro:
      'This section is where the relationship-driven forms start to matter. Each detail links one order with one product.',
    columns: [
      { label: 'Order', value: (record) => record.order?.order_number ?? 'No order linked' },
      { label: 'Product', value: (record) => joinProduct(record.product) },
      { label: 'Quantity', value: (record) => record.quantity },
      { label: 'Price', value: (record) => formatMoney(record.price) },
    ],
    fields: [
      {
        key: 'order_id',
        label: 'Order',
        type: 'select',
        optionsResource: 'orders',
        optionLabel: (record) => record.order_number,
      },
      {
        key: 'product_id',
        label: 'Product',
        type: 'select',
        optionsResource: 'products',
        optionLabel: (record) => record.name,
      },
      { key: 'quantity', label: 'Quantity', type: 'number', step: '1', min: 1 },
      { key: 'price', label: 'Price', type: 'number', step: '0.01', min: 0 },
    ],
  },
  'supplier-products': {
    key: 'supplier-products',
    path: 'supplier-products',
    title: 'Supplier products',
    singular: 'supplier product',
    endpoint: '/api/v1/supplier-products',
    description: 'Map which supplier can provide each product in the catalog.',
    intro:
      'This resource is the matrix between the catalog and the sourcing layer. It is a great place to validate your relational UI.',
    columns: [
      { label: 'Supplier', value: (record) => joinSupplier(record.supplier) },
      { label: 'Product', value: (record) => joinProduct(record.product) },
    ],
    fields: [
      {
        key: 'supplier_id',
        label: 'Supplier',
        type: 'select',
        optionsResource: 'suppliers',
        optionLabel: (record) => record.name,
      },
      {
        key: 'product_id',
        label: 'Product',
        type: 'select',
        optionsResource: 'products',
        optionLabel: (record) => record.name,
      },
    ],
  },
}

export const resourceNavigation = Object.values(resourceConfigs).map((resource) => ({
  key: resource.key,
  path: resource.path,
  title: resource.title,
  description: resource.description,
}))

export function getResourceConfig(resourceKey) {
  return resourceConfigs[resourceKey]
}
