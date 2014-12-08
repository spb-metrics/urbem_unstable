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
  * Página que abre o preview do relatório desenvolvido no Birt.
  * Data de criação : 18/07/2008

  * @author Desenvolvedor: Diogo Zarpelon

  $Id: OCGeraRelatorioApoliceSeguros.php 59612 2014-09-02 12:00:51Z gelson $

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,6,3);
$preview->setVersaoBirt( '2.5.0' );

$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('apoliceSeguros');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );

// Seta o parâmetro cod_apolice no relatório.
if ( !empty($_REQUEST['inCodApolice']) ) {
    $preview->addParametro( 'cod_apolice', $_REQUEST['inCodApolice'] );
}

// Seta o parâmetro num_apolice no relatório.
if ( !empty($_REQUEST['inNumApolice']) ) {
    $preview->addParametro( 'num_apolice', $_REQUEST['inNumApolice'] );
}

// Seta o parâmetro num_cgm no relatório.
$preview->addParametro( 'num_cgm', $_REQUEST['inNumCGM']);

// Seta o parâmetro ordenacao no relatório.
if ( !empty($_REQUEST['stOrdenacao']) ) {
    $preview->addParametro( 'ordenacao', $_REQUEST['stOrdenacao'] );
}

$preview->preview();
