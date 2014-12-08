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
    * Classe de mapeamento da tabela tcepe.fonte_recurso
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    *
    $Id: TTCEPEFonteRecurso.class.php 60140 2014-10-01 17:18:39Z evandro $
    *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEFonteRecurso extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEFonteRecurso()
    {
        parent::Persistente();
        $this->setTabela('tcepe.fonte_recurso');

        $this->setCampoCod('cod_fonte');

        $this->AddCampo('cod_fonte'      , 'integer', true  , ''   , true, true);
        $this->AddCampo('descricao'      , 'varchar', true  , '50' , true, false);
        
    }

}