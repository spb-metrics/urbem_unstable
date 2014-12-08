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


    * Filtro para Relatorio de Inscricao da Divida Ativa
    * Data de Criação   : 09/09/2014    
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM    
    * $Id: OCGeraRelatorioInscricaoDividaAtiva.php 60555 2014-10-28 17:11:27Z carolina $

*/
set_time_limit(0);
ini_set('memory_limit', '2G');
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;

$obMPDF = new FrameWorkMPDF(5,33,7);
$obMPDF->setCodEntidades($inCodEntidades);
$obMPDF->setNomeRelatorio("Relatorio de Inscricoes da Divida Ativa");
$obMPDF->setFormatoFolha("A4");

$arDados = Sessao::read('arDados');
$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();

?>
