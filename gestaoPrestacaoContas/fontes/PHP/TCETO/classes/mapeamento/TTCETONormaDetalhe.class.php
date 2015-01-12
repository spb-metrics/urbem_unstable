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
* Classe de Mapeamento para tabela norma
* Data de Criação: 25/07/2005

* @author Analista: Fabio 
* @author Desenvolvedor: Lisiane Morais

$Revision:
$Name$
$Author:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';


class TTCETONormaDetalhe extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCETONormaDetalhe()
{
    parent::Persistente();
    $this->setTabela('tceto.norma_detalhe');

    $this->setCampoCod('cod_norma');
    $this->setComplementoChave('');
    $this->AddCampo('cod_norma'                   ,'integer'     ,true,'',true,true);
    $this->AddCampo('cod_lei_alteracao'           ,'integer'     ,true,'',false,true);
    $this->AddCampo('cod_norma_alteracao'         ,'integer'     ,true,'',false,true);
    $this->AddCampo('percentual_credito_adicional','numeric(3,0)',true,'',false,false);

}

}