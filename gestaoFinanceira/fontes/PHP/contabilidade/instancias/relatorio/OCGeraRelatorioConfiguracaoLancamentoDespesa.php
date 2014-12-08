<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Filtro do relatório de Configuração de Lançamento de Despesa
    * Data de CriaÃ§Ã£o   : 17/11/2011

    * @author Analista Tonismar Bernardo
    * @author Desenvolvedor Davi Aroldi

    * @ignore

    * Casos de uso: uc-02.03.18

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

// include_once (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );

$preview = new PreviewBirt(2,9,7);
// $preview->setFormato("pdf");
$preview->setVersaoBirt( '2.5.0' );
$preview->setNomeRelatorio( 'relatorioConfiguracaoLancamentoDespesa' );

$preview->setTitulo("Relatório Configuração de Lançamento de Despesa");
$preview->addParametro( 'tipo_lancamento', $_POST['stTipoLancamento'] );
$preview->addParametro( 'cod_classificacao', $_POST['inCodDespesa'] );

$preview->preview();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
