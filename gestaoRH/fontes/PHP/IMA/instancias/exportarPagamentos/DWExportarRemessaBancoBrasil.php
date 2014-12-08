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
    * Página de Download
    * Data de Criação: 07/12/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: souzadl $
    $Date: 2006-12-07 08:19:22 -0200 (Qui, 07 Dez 2006) $

    * Casos de uso: uc-04.08.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CLA_ARQUIVO                       );

$stLink         = $_GET['arq'];
$stLabel        = $_GET['label'];
$inPosBarra     = strrpos   ($stLink,'/')                   ;
$stNomeArquivo  = substr    ($stLink,$inPosBarra+1)         ;

$obArquivo = new Arquivo(CAM_FRAMEWORK.'tmp/'.$stNomeArquivo,$stLabel);
$obArquivo->Show();

?>
