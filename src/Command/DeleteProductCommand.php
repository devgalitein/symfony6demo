<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

#[AsCommand(
    name: 'delete:product',
    description: 'Delete no of product by passing number_of_product option',
)]
class DeleteProductCommand extends Command
{
    
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
            ->addOption('number_of_product',null,InputOption::VALUE_REQUIRED, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('number_of_product')) {
            
            $limit = $input->getOption('number_of_product');
            $products = $this->entityManager->getRepository(Product::class)->findBy([], ['id'=>'ASC'], $limit, $offset = 0);
            foreach($products as $product) {
                $this->entityManager->remove($product);
                $this->entityManager->flush();
                 
                $io->note(sprintf('Deleted Product: %s', $product->getId()));
            }
        }

        $io->success('Command Successfully Executed');

        return Command::SUCCESS;
    }
}
