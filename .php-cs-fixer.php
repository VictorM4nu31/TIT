<?php declare(strict_types=1);

$config = new PhpCsFixer\Config();

return $config
  ->setRiskyAllowed(true)
  ->setRules([
    '@PSR12' => true,
    '@PSR12:risky' => true,
    'strict_param' => true,
    'declare_strict_types' => true,
    'single_quote' => true,
    'no_unused_imports' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'strict_comparison' => true,
    'final_class' => true,
    'native_function_invocation' => ['include' => ['@compiler_optimized']],
  ])
  ->setFinder(
    PhpCsFixer\Finder::create()
      ->in(__DIR__ . '/admi')
      ->in(__DIR__ . '/chec')
      ->in(__DIR__ . '/registr_pers')
      ->in(__DIR__ . '/registr_combis')
      ->in(__DIR__ . '/src')
      ->in(__DIR__ . '/tests')
      ->append([__DIR__ . '/reporte.php'])
      ->exclude('fpdf185')
      ->exclude('vendor')
  );
