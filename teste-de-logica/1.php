<?php

main();

/**
 * @return void
 */
function main(): void
{
    $items = readItems();

    $rules = new SplObjectStorage();

    $rules->attach(new CartRule(function (CartItem $item) {
        return $item->getQuantity() <= 5;
    }, 0.02));

    $rules->attach(new CartRule(function (CartItem $item) {
        return $item->getQuantity() > 5 && $item->getQuantity() <= 10;
    }, 0.03));

    $rules->attach(new CartRule(function (CartItem $item) {
        return $item->getQuantity() > 10;
    }, 0.05));

    $cart = new Cart($items, $rules);

    echo '########## PAGAMENTO ##########' . PHP_EOL;
    echo "Subtotal: {$cart->getSubTotal()}" . PHP_EOL;
    echo "Descontos: {$cart->getTotalDiscount()}" . PHP_EOL;
    echo "Total: {$cart->getTotal()}" . PHP_EOL;
}

/**
 * @return SplObjectStorage|CartItem[]
 */
function readItems(): SplObjectStorage
{
    echo '########## CARRINHO DE COMPRAS ##########' . PHP_EOL;
    $q = (int) readline('Quantos ítens pretende inserir? ');

    $items = new SplObjectStorage();

    for ($i = 1; $i <= $q; $i++) {
        echo "Cadastro do ítem #{$i}" . PHP_EOL;

        $name = (string) readline('Nome do produto: ');
        $price = (float) readline('Preço do produto: ');
        $quantity = (int) readline('Quantidade adquirida: ');

        $items->attach(new CartItem($name, $price, $quantity));
    }

    return $items;
}

/**
 * Class CartItem
 */
class CartItem
{
    /** @var string */
    private $name;

    /** @var float */
    private $unitPrice;

    /** @var int */
    private $quantity;

    /**
     * Item constructor.
     * @param string $name
     * @param float $unitPrice
     * @param int $quantity
     */
    public function __construct(string $name, float $unitPrice, int $quantity)
    {
        $this->name = $name;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}

/**
 * Class CartRule
 */
class CartRule
{
    /** @var callable */
    private $condition;

    /** @var float */
    private $discount;

    /**
     * CartRule constructor.
     * @param callable $condition
     * @param float $discount
     */
    public function __construct(callable $condition, float $discount)
    {
        $this->condition = $condition;
        $this->discount = $discount;
    }

    /**
     * @param CartItem $item
     * @return bool
     */
    public function match(CartItem $item)
    {
        return (bool) call_user_func($this->condition, $item);
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }
}

/**
 * Class Cart
 */
class Cart
{
    /** @var float */
    private $subTotal = 0.00;

    /** @var float */
    private $totalDiscount = 0.00;

    /** @var float */
    private $total = 0.00;

    /**
     * Cart constructor.
     * @param SplObjectStorage|CartItem[] $items
     * @param SplObjectStorage|CartRule[] $rules
     */
    public function __construct(SplObjectStorage $items, SplObjectStorage $rules)
    {
        foreach ($items as $item) {
            assert($item instanceof CartItem);
            $this->subTotal += $item->getQuantity() * $item->getUnitPrice();
            foreach ($rules as $rule) {
                assert($rule instanceof CartRule);
                if ($rule->match($item)) {
                    $this->totalDiscount += $item->getQuantity() * $item->getUnitPrice() * $rule->getDiscount();
                }
            }
        }

        $this->total = $this->subTotal - $this->totalDiscount;
    }

    /**
     * @return float
     */
    public function getSubTotal(): float
    {
        return $this->subTotal;
    }

    /**
     * @return float
     */
    public function getTotalDiscount(): float
    {
        return $this->totalDiscount;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }
}
