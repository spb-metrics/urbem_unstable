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
 * Página de Processamento de regras de Configuração e de Homologação
 * Data de Criação: 02/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Heleno Menezes dos Santos <heleno.santos>
 * @author Pedro de Medeiros <pedro.medeiros>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.10.01 / uc-02.10.02
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require CAM_GF_LDO_NEGOCIO.'RLDOValidarAcao.class.php';
require CAM_GF_LDO_VISAO.'VLDOValidarAcao.class.php';

$obView = new VLDOValidarAcao(new RLDOValidarAcao);
$obView->$_REQUEST["stAcao"]($_REQUEST);

SistemaLegado::LiberaFrames();
