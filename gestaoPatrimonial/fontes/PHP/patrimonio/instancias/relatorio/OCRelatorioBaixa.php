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
  * Página de frame oculto para relatório de baixa de bens
  * Data de criação : 28/10/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    Caso de uso: uc-03.01.09
**/

/*
$Log$
Revision 1.5  2006/07/06 14:07:05  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

include_once( CAM_GP_PAT_NEGOCIO."RPatrimonioRelatorioBaixa.class.php");
include (CAM_GP_PAT_NEGOCIO."RPatrimonioAtributoPatrimonio.class.php");

$preview = new PreviewBirt(3,6,20);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');
$preview->setNomeArquivo('relatorioBemBaixado');

if ($_REQUEST['inCodAtributo'] != '') {
    $preview->addParametro( 'cod_atributo', $_REQUEST['inCodAtributo'] );

    //Utiliza este trecho de código para pegar o nome do atributo e enviar direto para a coluna do relatório
    $atributosPatrimonio = new RPatrimonioAtributoPatrimonio;
    $atributosPatrimonio->listar($atributo);

    for ($i=0; $i<count($atributo->arElementos); $i++) {
        if ($_REQUEST['inCodAtributo'] == $atributo->arElementos[$i]['cod_atributo']) {
            $preview->addParametro( 'nom_atributo', strtoupper($atributo->arElementos[$i]['nom_atributo']));
        }
    }

} else {
    $preview->addParametro( 'cod_atributo', '' );
}

if ($_REQUEST['inCodOrdem'] != '') {
    $preview->addParametro( 'cod_ordem', $_REQUEST['inCodOrdem'] );
} else {
    $preview->addParametro( 'cod_ordem', '' );
}

if ($_REQUEST['stDataInicial'] != '') {
    $preview->addParametro( 'data_inicial', $_REQUEST['stDataInicial'] );
} else {
    $preview->addParametro( 'data_inicial', '' );
}

if ($_REQUEST['stDataFinal'] != '') {
    $preview->addParametro( 'data_final', $_REQUEST['stDataFinal'] );
} else {
    $preview->addParametro( 'data_final', '' );
}

$preview->preview();
