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
    * Classe de mapeamento da função contabilidade.incluir_escolha_plano_contas
    * Data de Criação: 13/12/2013

    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeIncluirEscolhaPlanoContas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeIncluirEscolhaPlanoContas()
{
    parent::Persistente();
    $this->setTabela('contabilidade.incluir_escolha_plano_contas');

    $this->AddCampo('exercicio', 'varchar', false, '4', false, false);
    $this->AddCampo('cod_uf'   , 'integer', false, '' , false, false);
    $this->AddCampo('cod_plano', 'integer', false, '' , false, false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT ".$this->getTabela()."('".$this->getDado("exercicio")."', ".$this->getDado("cod_uf").", ".$this->getDado("cod_plano").") \n";

    return $stSql;
}

}
