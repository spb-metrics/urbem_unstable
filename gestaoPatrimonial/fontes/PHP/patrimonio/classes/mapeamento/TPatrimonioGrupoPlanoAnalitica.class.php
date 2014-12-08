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
    * Data de Criação: 05/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25536 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-18 12:11:18 -0300 (Ter, 18 Set 2007) $

    * Casos de uso: uc-03.01.04
*/

/*
$Log$
Revision 1.1  2007/09/18 15:10:55  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioGrupoPlanoAnalitica extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioGrupoPlanoAnalitica()
{
    parent::Persistente();
    $this->setTabela('patrimonio.grupo_plano_analitica');
    $this->setCampoCod('cod_grupo');
    $this->setComplementoChave('cod_natureza,exercicio,cod_plano');
    $this->AddCampo('cod_grupo','integer',true,'',true,false);
    $this->AddCampo('cod_natureza','integer',true,'',true,"TPatrimonioNatureza");
    $this->AddCampo('exercicio','varchar',true,'4',true,false);
    $this->AddCampo('cod_plano','integer',true,'',true,false);

}

}
